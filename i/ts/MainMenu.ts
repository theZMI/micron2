/// <reference path="./includes.ts" />

export class MainMenu {
    readonly MENU_SELECTOR = '#navbarMainMenu';

    private movePage() {
        let menu = $(this.MENU_SELECTOR);
        // @ts-ignore
        $(window).scrollTop() > 1
            ? menu.addClass('menu-in-move')
            : menu.removeClass('menu-in-move');
    }

    private clickByToggleButton() {
        let menu = $(this.MENU_SELECTOR);
        $(this.MENU_SELECTOR + 'ToggleButton').hasClass('collapsed')
            ? menu.removeClass('menu-is-open')
            : menu.addClass('menu-is-open');
    }

    private searchPanelToggleButton() {
        $(this.MENU_SELECTOR + ' .search-panel-button-toggle').on(
            'click',
            () => {
                let searchPanel = $('.search-panel');
                let otherMenus = $('.menu-left-part, .menu-right-part');
                let isSearchPanelOpen = searchPanel.is(':visible');

                if (isSearchPanelOpen) {
                    searchPanel
                        .fadeOut(
                            'normal',
                            () => {
                                otherMenus.fadeIn();
                            }
                        ).animate({
                        padding: "0.5rem"
                    });
                    $(this.MENU_SELECTOR).removeClass('menu-is-open');
                } else {
                    otherMenus.fadeOut(
                        'normal',
                        () => {
                            searchPanel
                                .removeClass('d-none')
                                .show()
                                .animate({
                                    opacity: 1,
                                    width: "100%",
                                    padding: "5rem 0.5rem"
                                })
                                .find('input').trigger('focus');
                        }
                    );
                    $(this.MENU_SELECTOR).addClass('menu-is-open');
                }

            }
        )
    }

    init() {
        $(() => {
            $(window).on(
                'scroll touchstart touchmove touchend',
                () => {
                    this.movePage();
                }
            );
            this.movePage();

            $(this.MENU_SELECTOR + 'ToggleButton').on(
                'click',
                () => {
                    this.clickByToggleButton();
                }
            );

            this.searchPanelToggleButton();
        });
    }
}