// FUNKCJE ZMIANY TŁA
function changeBackground(color) {
    document.body.style.backgroundColor = color;
    document.body.style.setProperty('background-color', color, 'important');
    localStorage.setItem('bgColor', color);
}

// KONWERSJA JEDNOSTEK
let decimal = 0;
let computed = false;

function addChar(input, character) {
    if ((character === "." && decimal === 0) || character !== ".") {
        input.value = input.value === "" || input.value === "0" ? character : input.value + character;
        convert(input.form, input.form.measure1, input.form.measure2);
        if (character === ".") decimal = 1;
    }
}

function clear(form) {
    form.input.value = 0;
    form.display.value = 0;
    decimal = 0;
}

// INICJALIZACJA
document.addEventListener('DOMContentLoaded', () => {
    const savedColor = localStorage.getItem('bgColor');
    if (savedColor) document.body.style.backgroundColor = savedColor;
    if (typeof startclock === 'function') startclock();
});

// LOGIKA KONWERSJI
function convert(entryform, from, to) {
    const convertFrom = from.options[from.selectedIndex].value;
    const convertTo = to.options[to.selectedIndex].value;
    entryform.display.value = (entryform.input.value * convertFrom / convertTo).toFixed(4);
}

function multiplyNumbers(a, b) {
    return a * b;
}