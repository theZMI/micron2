// Магазин: корзина, избранное и сравнение без перезагрузки, быстрый просмотр, галерея, количество (в т.ч. дробное),
// фильтры каталога, доставка в оформлении заказа, подсказки поиска.
// Разметка - tpl/shop/*. Без JS всё работает обычными формами и ссылками
import $ from "jquery";

const ajaxPost = (url, data) => fetch(url, {
    method: 'POST',
    body: data,
    headers: {'X-Requested-With': 'XMLHttpRequest'},
    credentials: 'same-origin',
}).then(r => r.json().catch(() => Promise.reject(r))).then(json => json.is_success ? json.data : Promise.reject(json));

const escapeHtml = s => String(s).replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));

// Счётчики корзины и избранного в меню
function updateHeader(state) {
    document.querySelectorAll('[data-cart-count]').forEach(el => {
        el.textContent = state.cart_count;
        el.classList.toggle('d-none', !state.cart_count);
    });
    document.querySelectorAll('[data-cart-total]').forEach(el => {
        el.textContent = state.cart_total_text;
        el.classList.toggle('d-none', !state.cart_count);
    });
    document.querySelectorAll('[data-favorites-count]').forEach(el => {
        el.textContent = state.favorites_count;
        el.classList.toggle('d-none', !state.favorites_count);
    });
    if (state.compare_count !== undefined) {
        document.querySelectorAll('[data-compare-count]').forEach(el => {
            el.textContent = state.compare_count;
            el.classList.toggle('d-none', !state.compare_count);
        });
        document.querySelectorAll('[data-compare-link]').forEach(el => el.closest('li')?.classList.toggle('d-none', !state.compare_count));
    }
}

// Сумма в копейках как в PHP (ShopPrice): 162000 -> «1 620 ₽», копейки - только если есть. Деньги везде - целые копейки
const formatPrice = (kopecks, sign) => {
    const hasCents = kopecks % 100 !== 0;
    const [int, cents] = (kopecks / 100).toFixed(hasCents ? 2 : 0).split('.');
    return int.replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + (cents ? '.' + cents : '') + ' ' + sign;
};

function toast(html) {
    const box = document.querySelector('[data-toasts]');
    if (!box || !window.bootstrap) {
        return;
    }
    const el = document.createElement('div');
    el.className = 'toast align-items-center';
    el.setAttribute('role', 'status');
    el.innerHTML = `<div class="d-flex"><div class="toast-body">${html}</div><button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Закрыть"></button></div>`;
    box.appendChild(el);
    el.addEventListener('hidden.bs.toast', () => el.remove());
    new window.bootstrap.Toast(el, {delay: 3000}).show();
}

// В корзину (формы data-cart-form)
$(document).on('submit', 'form[data-cart-form]', function (e) {
    e.preventDefault();
    const form = this;
    const button = form.querySelector('[type=submit]');
    button && (button.disabled = true);
    ajaxPost(form.action, new FormData(form))
        .then(state => {
            updateHeader(state);
            const cartUrl = document.querySelector('[data-toasts]')?.dataset.cartUrl || form.action;
            toast(`<i class="bi bi-bag-check me-2"></i>Добавлено в корзину · ${escapeHtml(state.cart_total_text)} <a href="${cartUrl}" class="ms-2 fw-semibold">Оформить</a>`);
        })
        .catch(err => {
            if (err && err.is_error && err.data && err.data.message) { // Сервер отказал (например, товар закончился)
                updateHeader(err.data);
                toast(`<i class="bi bi-exclamation-circle me-2"></i>${escapeHtml(err.data.message)}`);
            } else {
                form.submit(); // Не получилось связаться - отправляем обычной формой
            }
        })
        .finally(() => button && (button.disabled = false));
});

// Избранное (кнопки data-favorite="id")
$(document).on('click', '[data-favorite]', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const id = this.dataset.favorite;
    const url = document.querySelector('[data-toasts]')?.dataset.favoritesUrl;
    const data = new FormData();
    data.append('a', 'toggle');
    data.append('product_id', id);
    ajaxPost(url, data).then(state => {
        updateHeader(state);
        document.querySelectorAll(`[data-favorite="${id}"]`).forEach(btn => btn.classList.toggle('is-active', state.is_favorite));
        toast(state.is_favorite
            ? `<i class="bi bi-heart-fill me-2"></i>Добавлено в избранное <a href="${url}" class="ms-2 fw-semibold">Открыть</a>`
            : 'Убрано из избранного');
    });
});

// Быстрый просмотр
$(document).on('click', '[data-quick-view]', function (e) {
    const modalEl = document.getElementById('quickViewModal');
    if (!modalEl || !window.bootstrap) {
        return;
    }
    e.preventDefault();
    const body = modalEl.querySelector('[data-quick-view-body]');
    body.innerHTML = '<div class="quick-view-loading"><div class="spinner-border" role="status"></div></div>';
    window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    const url = modalEl.dataset.url + (modalEl.dataset.url.includes('?') ? '&' : '?') + 'id=' + encodeURIComponent(this.dataset.quickView);
    fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'})
        .then(r => r.ok ? r.text() : Promise.reject(r))
        .then(html => body.innerHTML = html)
        .catch(() => body.innerHTML = '<p class="text-center text-secondary my-5">Не удалось загрузить товар</p>');
});

// Галерея: переключение главного фото
$(document).on('click', '[data-gallery-thumb]', function () {
    const gallery = this.closest('[data-gallery]');
    gallery.querySelector('[data-gallery-main]').src = this.dataset.galleryThumb;
    gallery.querySelectorAll('[data-gallery-thumb]').forEach(btn => btn.classList.toggle('is-active', btn === this));
});

// Сравнение (кнопки data-compare="id"; data-compare-reload - на странице сравнения, перезагрузить её)
$(document).on('click', '[data-compare]', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const id = this.dataset.compare;
    const reload = this.hasAttribute('data-compare-reload');
    const url = document.querySelector('[data-toasts]')?.dataset.compareUrl;
    const data = new FormData();
    data.append('a', 'toggle');
    data.append('product_id', id);
    ajaxPost(url, data).then(state => {
        if (reload) {
            location.reload();
            return;
        }
        updateHeader(state);
        document.querySelectorAll(`[data-compare="${id}"]`).forEach(btn => btn.classList.toggle('is-active', state.is_compare));
        toast(state.is_compare
            ? `<i class="bi bi-bar-chart-fill me-2"></i>Добавлено в сравнение <a href="${url}" class="ms-2 fw-semibold">Сравнить</a>`
            : 'Убрано из сравнения');
    });
});

// Кнопки −/+ у количества: шаг и минимум - из step/min (бывают дробные: 0.1 м). В корзине ниже минимума товара (data-min) - 0, то есть удалить.
// В корзине (data-autosubmit) пересчитываем с небольшой задержкой
let autosubmitTimer = null;
const round3 = v => Math.round(v * 1000) / 1000;
$(document).on('click', '.qty-input [data-qty]', function () {
    const input = this.closest('.qty-input').querySelector('input');
    const step = parseFloat(input.step) || 1;
    const min = parseFloat(input.min || '0');
    const max = parseFloat(input.max || '999');
    const itemMin = parseFloat(input.dataset.min || '0');
    let value = round3((parseFloat(String(input.value).replace(',', '.')) || 0) + step * parseInt(this.dataset.qty, 10));
    if (itemMin && value > 0 && value < itemMin) {
        value = this.dataset.qty > 0 ? itemMin : 0;
    }
    input.value = round3(Math.min(max, Math.max(min, value)));
    $(input).trigger('change');
});
$(document).on('change', 'input[data-autosubmit]', function () {
    clearTimeout(autosubmitTimer);
    autosubmitTimer = setTimeout(() => this.form.submit(), 400);
});

// Каталог: фильтры и сортировка применяются сразу
$(document).on('change', 'form[data-catalog-filter] input[type=checkbox], select[data-catalog-sort]', function () {
    this.form.submit(); // У select сортировки форма задана атрибутом form="shopFilters"
});

// Оформление заказа: адрес - только для доставки, которой он нужен; стоимость доставки и итог - сразу; текст кнопки - по способу оплаты
$(document).on('change', 'form[data-checkout] input[name=delivery_method], form[data-checkout] input[name=payment_method]', function () {
    const form = this.form;
    const delivery = form.querySelector('input[name=delivery_method]:checked');
    if (delivery) {
        const needAddress = delivery.dataset.needAddress === '1';
        const addressBlock = form.querySelector('[data-address-block]');
        addressBlock.hidden = !needAddress;
        addressBlock.querySelector('textarea').required = needAddress;
        const price = parseInt(delivery.dataset.price, 10) || 0;
        document.querySelectorAll('[data-delivery-price]').forEach(el => el.textContent = delivery.dataset.priceText);
        document.querySelectorAll('[data-total]').forEach(el => {
            el.textContent = formatPrice(parseInt(el.dataset.totalWithoutDelivery, 10) + price, form.dataset.currencySign);
        });
    }
    const payment = form.querySelector('input[name=payment_method]:checked');
    const button = form.querySelector('[data-submit-online]');
    if (payment && button) {
        button.textContent = payment.dataset.online === '1' ? button.dataset.submitOnline : button.dataset.submitOffline;
    }
});

// Подсказки в строке поиска в шапке: товары и категории по мере ввода (скрипт может загрузиться раньше меню - ждём DOM)
const initSearchSuggest = () => {
    const form = document.querySelector('form[data-search-suggest]');
    const input = form?.querySelector('input[name=q]');
    if (!form || !input) {
        return;
    }
    const box = document.createElement('div');
    box.className = 'search-suggest d-none';
    box.setAttribute('role', 'listbox');
    form.querySelector('.input-group').appendChild(box);

    let timer = null;
    let lastQuery = '';
    let controller = null;
    const hide = () => box.classList.add('d-none');
    const render = data => {
        if (!data.items.length && !data.categories.length) {
            box.innerHTML = `<div class="search-suggest-empty">Ничего не нашлось</div>`;
        } else {
            const fixed = data.query.toLowerCase() !== input.value.trim().toLowerCase()
                ? `<div class="search-suggest-note">Результаты по запросу «${escapeHtml(data.query)}»</div>` : '';
            const categories = data.categories.map(c => `<a href="${c.url}" class="search-suggest-category"><i class="bi bi-folder2"></i>${escapeHtml(c.name)}</a>`).join('');
            const items = data.items.map(i => `<a href="${i.url}" class="search-suggest-item">
                ${i.image ? `<img src="${i.image}" alt="" loading="lazy">` : '<span class="search-suggest-noimage"><i class="bi bi-image"></i></span>'}
                <span class="search-suggest-name">${escapeHtml(i.name)}</span><span class="search-suggest-price">${escapeHtml(i.price)}</span></a>`).join('');
            const all = data.total > data.items.length ? `<a href="${data.all_url}" class="search-suggest-all">Все результаты: ${data.total} <i class="bi bi-arrow-right"></i></a>` : '';
            box.innerHTML = fixed + categories + items + all;
        }
        box.classList.remove('d-none');
    };
    input.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) {
            hide();
            lastQuery = '';
            return;
        }
        timer = setTimeout(() => {
            if (q === lastQuery) {
                box.classList.remove('d-none');
                return;
            }
            lastQuery = q;
            controller?.abort();
            controller = new AbortController();
            // Тот же домен, что у страницы (SiteRoot даёт абсолютный адрес, а сайт могли открыть с www или по другому имени)
            const url = new URL(form.dataset.searchSuggest, location.href);
            url.searchParams.set('q', q);
            fetch(url.pathname + url.search, {headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin', signal: controller.signal})
                .then(r => r.json())
                .then(json => json.is_success && input.value.trim() === q && render(json.data))
                .catch(() => {});
        }, 200);
    });
    input.addEventListener('focus', () => input.value.trim().length >= 2 && lastQuery && box.classList.remove('d-none'));
    document.addEventListener('click', e => !form.contains(e.target) && hide());
    input.addEventListener('keydown', e => {
        const links = [...box.querySelectorAll('a')];
        if (e.key === 'Escape') {
            hide();
        } else if (e.key === 'ArrowDown' && links.length && !box.classList.contains('d-none')) {
            e.preventDefault();
            links[0].focus();
        }
    });
    box.addEventListener('keydown', e => {
        const links = [...box.querySelectorAll('a')];
        const i = links.indexOf(document.activeElement);
        if (e.key === 'ArrowDown' && i < links.length - 1) {
            e.preventDefault();
            links[i + 1].focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            i > 0 ? links[i - 1].focus() : input.focus();
        } else if (e.key === 'Escape') {
            hide();
            input.focus();
        }
    });
};
document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', initSearchSuggest) : initSearchSuggest();

// Доп. товары/услуги на карточке: цена товара сразу показывается с выбранными доп. услугами
$(document).on('change', '.product-addons input[data-addon-price]', function () {
    const info = this.closest('.product-info');
    const priceBox = info?.querySelector('.product-price[data-base-price]');
    if (!priceBox) {
        return;
    }
    let addons = 0;
    info.querySelectorAll('.product-addons input[data-addon-price]:checked').forEach(input => addons += parseInt(input.dataset.addonPrice, 10) || 0);
    const sign = priceBox.dataset.currencySign;
    if (priceBox.dataset.addonsPerUnit === '1') { // Штучный товар: доп. услуги - к цене за штуку
        priceBox.querySelector('[data-price-amount]').textContent = formatPrice(parseInt(priceBox.dataset.basePrice, 10) + addons, sign);
    } else { // Метражный/весовой: доп. услуги - одна на позицию, отдельной строкой
        const sum = priceBox.querySelector('[data-addons-sum]');
        sum.hidden = !addons;
        sum.textContent = `+ ${formatPrice(addons, sign)} за доп. услуги`;
    }
});
