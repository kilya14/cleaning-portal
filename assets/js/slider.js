class AutoSlider {
    constructor(container, interval = 3000) {
        this.wrapper = document.querySelector(container);
        this.container = this.wrapper?.querySelector('.slider-container') || document.querySelector(container);
        if (!this.container) return;
        this.slides = this.container.querySelectorAll('.slide');
        this.currentIndex = 0;
        this.interval = interval;
        this.timer = null;
        this.init();
    }

    init() {
        this.showSlide(0);
        this.createDots();
        this.startAutoPlay();

        const prevBtn = this.container.querySelector('.slider-btn.prev, .prev');
        const nextBtn = this.container.querySelector('.slider-btn.next, .next');
        if (prevBtn) prevBtn.addEventListener('click', () => { this.prev(); this.resetAutoPlay(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { this.next(); this.resetAutoPlay(); });

        // Пауза при наведении
        this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
        this.container.addEventListener('mouseleave', () => this.startAutoPlay());
    }

    createDots() {
        const dotsContainer = this.wrapper?.querySelector('.slider-dots') || this.container.parentElement?.querySelector('.slider-dots');
        if (!dotsContainer || this.slides.length < 2) return;

        dotsContainer.innerHTML = '';
        this.slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'slider-dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('aria-label', `Слайд ${i + 1}`);
            dot.addEventListener('click', () => {
                this.currentIndex = i;
                this.showSlide(i);
                this.resetAutoPlay();
            });
            dotsContainer.appendChild(dot);
        });
    }

    updateDots() {
        const dots = (this.wrapper || this.container.parentElement)?.querySelectorAll('.slider-dot');
        dots?.forEach((dot, i) => dot.classList.toggle('active', i === this.currentIndex));
    }

    showSlide(index) {
        this.currentIndex = index;
        this.slides.forEach(slide => slide.classList.remove('active'));
        this.slides[index].classList.add('active');
        this.updateDots();
    }

    next() {
        this.currentIndex = (this.currentIndex + 1) % this.slides.length;
        this.showSlide(this.currentIndex);
    }

    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
        this.showSlide(this.currentIndex);
    }

    startAutoPlay() {
        this.stopAutoPlay();
        this.timer = setInterval(() => this.next(), this.interval);
    }

    stopAutoPlay() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    resetAutoPlay() {
        this.startAutoPlay();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.querySelector('.slider-wrapper');
    const selector = wrapper ? '.slider-wrapper' : '.slider-container';
    new AutoSlider(selector, 3000);
});
