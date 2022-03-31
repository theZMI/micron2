/// <reference path="./includes.ts" />

export class MainMenu {
    readonly MENU_SELECTOR = '#navbarMainMenu';

    private movePage() {
        let menu = $(this.MENU_SELECTOR);
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
                        );
                } else {
                    otherMenus.fadeOut(
                        'normal',
                        () => {
                            searchPanel
                                .removeClass('d-none')
                                .show()
                                .animate({
                                    opacity: 1,
                                    width: "100%"
                                })
                                .trigger('focus');
                        }
                    );
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

