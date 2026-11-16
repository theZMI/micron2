// Магазин: корзина и избранное без перезагрузки, быстрый просмотр, галерея, количество, фильтры каталога.
// Разметка - tpl/shop/*. Без JS всё работает обычными формами и ссылками
import $ from "jquery";

const ajaxPost = (url, data) => fetch(url, {
    method: 'POST',
    body: data,
    headers: {'X-Requested-With': 'XMLHttpRequest'},
    credentials: 'same-origin',
}).then(r => r.ok ? r.json() : Promise.reject(r)).then(json => json.is_success ? json.data : Promise.reject(json));

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
}

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
        .catch(() => form.submit()) // Не получилось - отправляем обычной формой
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

// Кнопки −/+ у количества. В корзине (data-autosubmit) пересчитываем с небольшой задержкой
let autosubmitTimer = null;
$(document).on('click', '.qty-input [data-qty]', function () {
    const input = this.closest('.qty-input').querySelector('input');
    const min = parseInt(input.min || '0', 10);
    const max = parseInt(input.max || '999', 10);
    input.value = Math.min(max, Math.max(min, (parseInt(input.value, 10) || 0) + parseInt(this.dataset.qty, 10)));
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
