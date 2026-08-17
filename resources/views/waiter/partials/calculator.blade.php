<div x-data="calculator()" class="rounded-3xl p-5 sticky top-6 w-full" style="background:#1C1712; border:1px solid rgba(227,168,87,0.3);">

    <p class="text-xs uppercase tracking-wide mb-3 font-semibold" style="color:#E3A857;">Calculator</p>

    {{-- Display --}}
    <div class="rounded-xl px-4 py-4 mb-4 text-right" style="background:#0F0C08; border:1px solid rgba(227,168,87,0.3);">
        <p class="text-xs h-4" style="color:rgba(227,168,87,0.65); font-family:'IBM Plex Mono',monospace;" x-text="expression"></p>
        <p class="text-3xl font-semibold truncate" style="color:#E3A857; font-family:'IBM Plex Mono',monospace;" x-text="display"></p>
    </div>

    {{-- Keypad --}}
    <div class="grid grid-cols-4 gap-2">
        <button type="button" @click="clearAll()" class="calc-btn calc-btn--op col-span-2">C</button>
        <button type="button" @click="backspace()" class="calc-btn calc-btn--op">⌫</button>
        <button type="button" @click="chooseOperator('÷')" class="calc-btn calc-btn--op">÷</button>

        <button type="button" @click="inputDigit('7')" class="calc-btn">7</button>
        <button type="button" @click="inputDigit('8')" class="calc-btn">8</button>
        <button type="button" @click="inputDigit('9')" class="calc-btn">9</button>
        <button type="button" @click="chooseOperator('×')" class="calc-btn calc-btn--op">×</button>

        <button type="button" @click="inputDigit('4')" class="calc-btn">4</button>
        <button type="button" @click="inputDigit('5')" class="calc-btn">5</button>
        <button type="button" @click="inputDigit('6')" class="calc-btn">6</button>
        <button type="button" @click="chooseOperator('-')" class="calc-btn calc-btn--op">−</button>

        <button type="button" @click="inputDigit('1')" class="calc-btn">1</button>
        <button type="button" @click="inputDigit('2')" class="calc-btn">2</button>
        <button type="button" @click="inputDigit('3')" class="calc-btn">3</button>
        <button type="button" @click="chooseOperator('+')" class="calc-btn calc-btn--op">+</button>

        <button type="button" @click="inputDigit('0')" class="calc-btn">0</button>
        <button type="button" @click="inputDigit('00')" class="calc-btn">00</button>
        <button type="button" @click="inputDigit('000')" class="calc-btn">000</button>
        <button type="button" @click="inputDecimal()" class="calc-btn calc-btn--op">.</button>

        <button type="button" @click="calculate()" class="calc-btn calc-btn--equals col-span-4">=</button>
    </div>
</div>

<style>
    .calc-btn {
        padding: 0.9rem 0;
        border-radius: 0.9rem;
        font-family: 'IBM Plex Mono', monospace;
        font-weight: 600;
        font-size: 1.05rem;
        line-height: 1;
        color: #F7F3EC !important;
        background: #2A2013 !important;
        border: 1px solid rgba(227,168,87,0.22);
        transition: transform 0.08s ease, background 0.15s ease;
    }
    .calc-btn:active { transform: scale(0.94); }
    .calc-btn:hover { background: #362A18 !important; }
    .calc-btn--op {
        color: #E3A857 !important;
        background: #33280F !important;
        border-color: rgba(227,168,87,0.4);
    }
    .calc-btn--equals {
        background: #E3A857 !important;
        color: #14110D !important;
        border-color: #E3A857;
        font-size: 1.2rem;
    }
    .calc-btn--equals:hover { background: #f0ba6c !important; }
</style>

<script>
    function calculator() {
        return {
            display: '0',
            expression: '',
            firstValue: null,
            operator: null,
            waitingForSecond: false,

            inputDigit(d) {
                if (this.waitingForSecond) {
                    this.display = /^0+$/.test(d) ? '0' : d;
                    this.waitingForSecond = false;
                    return;
                }
                if (this.display === '0') {
                    this.display = /^0+$/.test(d) ? '0' : d;
                } else {
                    this.display += d;
                }
            },
            inputDecimal() {
                if (this.waitingForSecond) {
                    this.display = '0.';
                    this.waitingForSecond = false;
                    return;
                }
                if (!this.display.includes('.')) this.display += '.';
            },
            backspace() {
                this.display = this.display.length > 1 ? this.display.slice(0, -1) : '0';
            },
            clearAll() {
                this.display = '0';
                this.expression = '';
                this.firstValue = null;
                this.operator = null;
                this.waitingForSecond = false;
            },
            chooseOperator(op) {
                const value = parseFloat(this.display);
                if (this.firstValue === null) {
                    this.firstValue = value;
                } else if (!this.waitingForSecond) {
                    this.firstValue = this.compute(this.firstValue, value, this.operator);
                    this.display = String(this.firstValue);
                }
                this.operator = op;
                this.expression = `${this.firstValue} ${op}`;
                this.waitingForSecond = true;
            },
            calculate() {
                if (this.operator === null || this.firstValue === null) return;
                const value = parseFloat(this.display);
                const result = this.compute(this.firstValue, value, this.operator);
                this.expression = `${this.firstValue} ${this.operator} ${value} =`;
                this.display = String(result);
                this.firstValue = null;
                this.operator = null;
                this.waitingForSecond = false;
            },
            compute(a, b, op) {
                switch (op) {
                    case '+': return a + b;
                    case '-': return a - b;
                    case '×': return a * b;
                    case '÷': return b === 0 ? 0 : a / b;
                    default: return b;
                }
            },
        }
    }
</script>