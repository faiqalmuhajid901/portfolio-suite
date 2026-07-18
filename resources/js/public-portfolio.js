document.addEventListener('alpine:init', () => {
    Alpine.data('publicPortfolio', (configuration = {}) => ({
        scrollY: window.scrollY,
        mouseX: 0,
        mouseY: 0,
        centerX: window.innerWidth / 2,
        centerY: window.innerHeight / 2,

        projectSearch: '',

        projectSearchIndex: Array.isArray(
            configuration.projectSearchIndex
        )
            ? configuration.projectSearchIndex
            : [],

        reducedMotion: false,
        mobileViewport: false,

        scrollFrame: null,
        resizeFrame: null,

        motionMediaQuery: null,
        mobileMediaQuery: null,

        scrollHandler: null,
        resizeHandler: null,
        motionPreferenceHandler: null,
        mobilePreferenceHandler: null,

        init() {
            this.motionMediaQuery = window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            );

            this.mobileMediaQuery = window.matchMedia(
                '(max-width: 767px)'
            );

            this.reducedMotion = this.motionMediaQuery.matches;
            this.mobileViewport = this.mobileMediaQuery.matches;

            this.updateViewportCenter();

            this.scrollHandler = () => {
                if (this.scrollFrame !== null) {
                    return;
                }

                this.scrollFrame = window.requestAnimationFrame(() => {
                    this.scrollY = window.scrollY;
                    this.scrollFrame = null;
                });
            };

            this.resizeHandler = () => {
                if (this.resizeFrame !== null) {
                    return;
                }

                this.resizeFrame = window.requestAnimationFrame(() => {
                    this.updateViewportCenter();

                    this.mobileViewport =
                        this.mobileMediaQuery.matches;

                    if (!this.motionEnabled) {
                        this.resetMouse();
                    }

                    this.resizeFrame = null;
                });
            };

            this.motionPreferenceHandler = (event) => {
                this.reducedMotion = event.matches;

                if (event.matches) {
                    this.resetMouse();
                }
            };

            this.mobilePreferenceHandler = (event) => {
                this.mobileViewport = event.matches;

                if (event.matches) {
                    this.resetMouse();
                }
            };

            window.addEventListener(
                'scroll',
                this.scrollHandler,
                {
                    passive: true,
                }
            );

            window.addEventListener(
                'resize',
                this.resizeHandler,
                {
                    passive: true,
                }
            );

            this.addMediaQueryListener(
                this.motionMediaQuery,
                this.motionPreferenceHandler
            );

            this.addMediaQueryListener(
                this.mobileMediaQuery,
                this.mobilePreferenceHandler
            );
        },

        destroy() {
            if (this.scrollHandler) {
                window.removeEventListener(
                    'scroll',
                    this.scrollHandler
                );
            }

            if (this.resizeHandler) {
                window.removeEventListener(
                    'resize',
                    this.resizeHandler
                );
            }

            this.removeMediaQueryListener(
                this.motionMediaQuery,
                this.motionPreferenceHandler
            );

            this.removeMediaQueryListener(
                this.mobileMediaQuery,
                this.mobilePreferenceHandler
            );

            if (this.scrollFrame !== null) {
                window.cancelAnimationFrame(
                    this.scrollFrame
                );
            }

            if (this.resizeFrame !== null) {
                window.cancelAnimationFrame(
                    this.resizeFrame
                );
            }
        },

        addMediaQueryListener(mediaQuery, handler) {
            if (!mediaQuery || !handler) {
                return;
            }

            if (
                typeof mediaQuery.addEventListener ===
                'function'
            ) {
                mediaQuery.addEventListener(
                    'change',
                    handler
                );

                return;
            }

            mediaQuery.addListener(handler);
        },

        removeMediaQueryListener(mediaQuery, handler) {
            if (!mediaQuery || !handler) {
                return;
            }

            if (
                typeof mediaQuery.removeEventListener ===
                'function'
            ) {
                mediaQuery.removeEventListener(
                    'change',
                    handler
                );

                return;
            }

            mediaQuery.removeListener(handler);
        },

        updateViewportCenter() {
            this.centerX = Math.max(
                window.innerWidth / 2,
                1
            );

            this.centerY = Math.max(
                window.innerHeight / 2,
                1
            );
        },

        get motionEnabled() {
            return (
                !this.reducedMotion &&
                !this.mobileViewport
            );
        },

        updateMouse(event) {
            if (!this.motionEnabled) {
                return;
            }

            this.mouseX = this.clamp(
                (
                    event.clientX -
                    this.centerX
                ) / this.centerX,
                -1,
                1
            );

            this.mouseY = this.clamp(
                (
                    event.clientY -
                    this.centerY
                ) / this.centerY,
                -1,
                1
            );
        },

        resetMouse() {
            this.mouseX = 0;
            this.mouseY = 0;
        },

        clamp(value, minimum, maximum) {
            return Math.min(
                Math.max(value, minimum),
                maximum
            );
        },

        heroBackgroundStyle() {
            if (!this.motionEnabled) {
                return {
                    transform: 'scale(1.025)',
                };
            }

            const scrollScale = Math.min(
                this.scrollY / 18000,
                0.025
            );

            return {
                transform: `
                    scale(${1.025 + scrollScale})
                    translate3d(
                        ${this.mouseX * -5}px,
                        ${this.mouseY * -4}px,
                        0
                    )
                `,
                transition:
                    'transform 180ms ease-out',
            };
        },

        heroGlowStyle(direction = 1) {
            if (!this.motionEnabled) {
                return {
                    transform: 'none',
                };
            }

            return {
                transform: `
                    translate3d(
                        ${this.mouseX * 14 * direction}px,
                        ${this.mouseY * 10 * direction}px,
                        0
                    )
                `,
                transition:
                    'transform 180ms ease-out',
            };
        },

        heroContentStyle() {
            if (!this.motionEnabled) {
                return {
                    transform: 'none',
                };
            }

            return {
                transform: `
                    rotateX(
                        ${this.clamp(
                            this.mouseY * -1.3,
                            -1.3,
                            1.3
                        )}deg
                    )
                    rotateY(
                        ${this.clamp(
                            this.mouseX * 1.6,
                            -1.6,
                            1.6
                        )}deg
                    )
                    translateY(
                        ${Math.min(
                            this.scrollY / 80,
                            12
                        )}px
                    )
                `,
                transition:
                    'transform 170ms ease-out',
            };
        },

        heroCardStyle(direction = 1) {
            if (!this.motionEnabled) {
                return {
                    transform: 'none',
                };
            }

            return {
                transform: `
                    rotateY(
                        ${this.clamp(
                            this.mouseX *
                                1.4 *
                                direction,
                            -1.4,
                            1.4
                        )}deg
                    )
                    rotateX(
                        ${this.clamp(
                            this.mouseY * -1,
                            -1,
                            1
                        )}deg
                    )
                `,
                transition:
                    'transform 170ms ease-out',
            };
        },

        avatarStyle() {
            if (!this.motionEnabled) {
                return {
                    transform: 'none',
                };
            }

            return {
                transform: `
                    rotateX(
                        ${this.clamp(
                            this.mouseY * -2,
                            -2,
                            2
                        )}deg
                    )
                    rotateY(
                        ${this.clamp(
                            this.mouseX * 2,
                            -2,
                            2
                        )}deg
                    )
                `,
                transition:
                    'transform 170ms ease-out',
            };
        },

        heroSummaryStyle() {
            if (!this.motionEnabled) {
                return {
                    transform: 'none',
                };
            }

            return {
                transform: `
                    translateY(
                        ${Math.min(
                            this.scrollY / 100,
                            8
                        )}px
                    )
                `,
                transition:
                    'transform 170ms ease-out',
            };
        },

        normalizeSearchValue(value) {
            return String(value ?? '')
                .toLocaleLowerCase('id-ID')
                .trim();
        },

        matchesProject(projectId) {
            const query = this.normalizeSearchValue(
                this.projectSearch
            );

            if (query === '') {
                return true;
            }

            const item = this.projectSearchIndex.find(
                (project) => {
                    return (
                        Number(project.id) ===
                        Number(projectId)
                    );
                }
            );

            if (!item) {
                return false;
            }

            return this.normalizeSearchValue(
                item.search
            ).includes(query);
        },

        get hasProjectResults() {
            const query = this.normalizeSearchValue(
                this.projectSearch
            );

            if (query === '') {
                return (
                    this.projectSearchIndex.length > 0
                );
            }

            return this.projectSearchIndex.some(
                (project) => {
                    return this.normalizeSearchValue(
                        project.search
                    ).includes(query);
                }
            );
        },

        clearProjectSearch() {
            this.projectSearch = '';
        },

        scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: this.reducedMotion
                    ? 'auto'
                    : 'smooth',
            });
        },
    }));
});
