const MIN_SWIPE_OFFSET = 100;
const Y_OFFSET_WRONG_SWIPE_DETECT = 100;

export default {
    mounted(el, swiperLeftCallback, swipeRightCallback) {
        const touchEnd = (event, posXStart, posYStart) => {
            const posXEnd = event.changedTouches[0]?.clientX;
            const posYEnd = event.changedTouches[0]?.clientY;
            const xOffset = posXEnd - posXStart;
            const yOffset = posYEnd - posYStart;
            const isSwipeRight = xOffset > 0;

            if (event.changedTouches.length !== 1) { // We only care if one finger is used
                return;
            }
            if (Math.abs(xOffset) < MIN_SWIPE_OFFSET) { // If swipe was too short
                return;
            }
            if (Math.abs(yOffset) > Y_OFFSET_WRONG_SWIPE_DETECT) { // If it's error swipe (user move a finger by vertical axis more than do swipe
                return;
            }

            const needSwipe = isSwipeRight ? swipeRightCallback : swiperLeftCallback;
            needSwipe();
        }

        const touchStart = event => {
            if (event.changedTouches.length !== 1) { // We only care if one finger is used
                return;
            }
            const posXStart = event.changedTouches[0].clientX;
            const posYStart = event.changedTouches[0].clientY;

            addEventListener(
                'touchend',
                event => touchEnd(event, posXStart, posYStart),
                {
                    once: true
                }
            );
        }

        el.addEventListener(
            'touchstart',
            touchStart
        );
    }
}