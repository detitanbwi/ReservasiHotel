// public/calendar.js - Premium Custom Date Range Calendar Picker

class CustomDateRangePicker {
    constructor(container, options = {}) {
        this.container = typeof container === 'string' ? document.getElementById(container) : container;
        if (!this.container) return;

        this.options = Object.assign({
            xDaysLimit: 30,
            defaultCheckIn: '',
            defaultCheckOut: '',
            onSelect: null
        }, options);

        this.selectedStart = this.options.defaultCheckIn ? new Date(this.options.defaultCheckIn) : null;
        this.selectedEnd = this.options.defaultCheckOut ? new Date(this.options.defaultCheckOut) : null;

        // Ensure dates are parsed at midnight local time
        if (this.selectedStart) this.selectedStart.setHours(0,0,0,0);
        if (this.selectedEnd) this.selectedEnd.setHours(0,0,0,0);

        this.currentMonth = this.selectedStart ? new Date(this.selectedStart) : new Date();
        this.currentMonth.setDate(1);

        this.today = new Date();
        this.today.setHours(0,0,0,0);

        this.maxDate = new Date(this.today);
        this.maxDate.setDate(this.today.getDate() + this.options.xDaysLimit);

        this.hoverDate = null;

        this.initDOM();
        this.renderCalendar();
        this.bindEvents();
        this.updateInputDisplay();
    }

    initDOM() {
        this.container.classList.add('date-range-picker-container');

        // Input Selector element
        this.inputEl = document.createElement('button');
        this.inputEl.type = 'button';
        this.inputEl.className = 'date-range-picker-input';
        this.inputEl.innerHTML = `
            <span class="picker-value">Select Dates</span>
            <span style="font-size: 1.1rem; opacity: 0.8;">📅</span>
        `;
        this.container.appendChild(this.inputEl);

        // Popup panel element
        this.popupEl = document.createElement('div');
        this.popupEl.className = 'date-range-picker-popup';
        this.popupEl.innerHTML = `
            <div class="calendar-header">
                <button type="button" class="calendar-btn prev-month-btn">&lt;</button>
                <span class="calendar-month-title">June 2026</span>
                <button type="button" class="calendar-btn next-month-btn">&gt;</button>
            </div>
            <div class="calendar-weekdays">
                <div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div><div>Su</div>
            </div>
            <div class="calendar-days"></div>
            <div class="calendar-footer">
                <button type="button" class="calendar-footer-btn calendar-btn-reset">Reset</button>
                <button type="button" class="calendar-footer-btn calendar-btn-submit">Submit</button>
            </div>
        `;
        this.container.appendChild(this.popupEl);

        this.daysContainer = this.popupEl.querySelector('.calendar-days');
        this.monthTitle = this.popupEl.querySelector('.calendar-month-title');
    }

    renderCalendar() {
        this.daysContainer.innerHTML = '';
        const year = this.currentMonth.getFullYear();
        const month = this.currentMonth.getMonth();

        this.monthTitle.innerText = this.currentMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

        // Calculate days
        const firstDayIndex = this.currentMonth.getDay(); // 0 is Sun, 1 is Mo...
        // Adjust for Monday start of week: 0 -> Sun -> index 6, 1 -> Mo -> index 0, etc.
        const startOffset = firstDayIndex === 0 ? 6 : firstDayIndex - 1;

        const totalDays = new Date(year, month + 1, 0).getDate();

        // Empty cells for offset
        for (let i = 0; i < startOffset; i++) {
            const emptyCell = document.createElement('div');
            this.daysContainer.appendChild(emptyCell);
        }

        // Render days
        for (let day = 1; day <= totalDays; day++) {
            const date = new Date(year, month, day);
            date.setHours(0,0,0,0);

            const dayBtn = document.createElement('button');
            dayBtn.type = 'button';
            dayBtn.className = 'calendar-day';
            dayBtn.innerText = day;
            dayBtn.dataset.date = this.formatISODate(date);

            // Disable check: out of limit or before today
            if (date < this.today || date > this.maxDate) {
                dayBtn.classList.add('disabled');
            } else {
                // Check selection states
                if (this.selectedStart && date.getTime() === this.selectedStart.getTime()) {
                    dayBtn.classList.add('selected-start');
                }
                if (this.selectedEnd && date.getTime() === this.selectedEnd.getTime()) {
                    dayBtn.classList.add('selected-end');
                }

                // In-range state
                if (this.selectedStart && this.selectedEnd && date > this.selectedStart && date < this.selectedEnd) {
                    dayBtn.classList.add('in-range');
                }

                // Hover range state (if only start date selected)
                if (this.selectedStart && !this.selectedEnd && this.hoverDate && date > this.selectedStart && date <= this.hoverDate) {
                    dayBtn.classList.add('hover-range');
                }
            }

            this.daysContainer.appendChild(dayBtn);
        }
    }

    bindEvents() {
        // Toggle popup
        this.inputEl.addEventListener('click', (e) => {
            e.stopPropagation();
            // Close all other pickers
            document.querySelectorAll('.date-range-picker-popup').forEach(p => {
                if (p !== this.popupEl) p.classList.remove('active');
            });
            this.popupEl.classList.toggle('active');
        });

        // Prev month
        this.popupEl.querySelector('.prev-month-btn').addEventListener('click', (e) => {
            e.stopPropagation();
            this.currentMonth.setMonth(this.currentMonth.getMonth() - 1);
            this.renderCalendar();
        });

        // Next month
        this.popupEl.querySelector('.next-month-btn').addEventListener('click', (e) => {
            e.stopPropagation();
            this.currentMonth.setMonth(this.currentMonth.getMonth() + 1);
            this.renderCalendar();
        });

        // Click day
        this.daysContainer.addEventListener('click', (e) => {
            const btn = e.target.closest('.calendar-day');
            if (!btn || btn.classList.contains('disabled')) return;
            e.stopPropagation();

            const clickedDate = new Date(btn.dataset.date);
            clickedDate.setHours(0,0,0,0);

            if (!this.selectedStart || (this.selectedStart && this.selectedEnd)) {
                // First click: select start date
                this.selectedStart = clickedDate;
                this.selectedEnd = null;
            } else if (this.selectedStart && !this.selectedEnd) {
                // Second click: select end date
                if (clickedDate <= this.selectedStart) {
                    // Reset start date if clicked is earlier or same
                    this.selectedStart = clickedDate;
                } else {
                    // Check-out must be at least 1 day after check-in
                    this.selectedEnd = clickedDate;
                }
            }

            this.renderCalendar();
        });

        // Hover day
        this.daysContainer.addEventListener('mousemove', (e) => {
            const btn = e.target.closest('.calendar-day');
            if (!btn || btn.classList.contains('disabled')) {
                this.hoverDate = null;
                return;
            }

            const hovered = new Date(btn.dataset.date);
            hovered.setHours(0,0,0,0);

            if (this.selectedStart && !this.selectedEnd && hovered > this.selectedStart) {
                if (!this.hoverDate || this.hoverDate.getTime() !== hovered.getTime()) {
                    this.hoverDate = hovered;
                    this.renderCalendar();
                }
            }
        });

        this.daysContainer.addEventListener('mouseleave', () => {
            this.hoverDate = null;
            this.renderCalendar();
        });

        // Reset
        this.popupEl.querySelector('.calendar-btn-reset').addEventListener('click', (e) => {
            e.stopPropagation();
            this.selectedStart = null;
            this.selectedEnd = null;
            this.renderCalendar();
        });

        // Submit
        this.popupEl.querySelector('.calendar-btn-submit').addEventListener('click', (e) => {
            e.stopPropagation();
            if (!this.selectedStart || !this.selectedEnd) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please Select Dates',
                    text: 'Please choose both Check-In and Check-Out dates first.',
                    confirmButtonColor: '#b19453'
                });
                return;
            }
            this.popupEl.classList.remove('active');
            this.updateInputDisplay();

            if (this.options.onSelect) {
                this.options.onSelect(this.formatISODate(this.selectedStart), this.formatISODate(this.selectedEnd));
            }
        });

        // Click outside closes
        document.addEventListener('click', (e) => {
            if (!this.container.contains(e.target)) {
                this.popupEl.classList.remove('active');
            }
        });
    }

    updateInputDisplay() {
        const valSpan = this.inputEl.querySelector('.picker-value');
        if (this.selectedStart && this.selectedEnd) {
            const options = { day: '2-digit', month: 'short', year: 'numeric' };
            const startStr = this.selectedStart.toLocaleDateString('en-GB', options);
            const endStr = this.selectedEnd.toLocaleDateString('en-GB', options);
            valSpan.innerText = `${startStr} - ${endStr}`;
            this.inputEl.style.borderColor = 'var(--color-bronze)';
        } else {
            valSpan.innerText = 'Select Dates';
            this.inputEl.style.borderColor = 'var(--color-bronze-light)';
        }
    }

    formatISODate(date) {
        if (!date) return '';
        const offset = date.getTimezoneOffset();
        const local = new Date(date.getTime() - (offset*60*1000));
        return local.toISOString().split('T')[0];
    }
}
