const leftOddPatterns = [
    '0001101', '0011001', '0010011', '0111101', '0100011',
    '0110001', '0101111', '0111011', '0110111', '0001011',
];

const leftEvenPatterns = [
    '0100111', '0110011', '0011011', '0100001', '0011101',
    '0111001', '0000101', '0010001', '0001001', '0010111',
];

const rightPatterns = [
    '1110010', '1100110', '1101100', '1000010', '1011100',
    '1001110', '1010000', '1000100', '1001000', '1110100',
];

const firstDigitParity = [
    'OOOOOO', 'OOEOEE', 'OOEEOE', 'OOEEEO', 'OEOOEE',
    'OEEOOE', 'OEEEOO', 'OEOEOE', 'OEOEEO', 'OEEOEO',
];

function getEan13Modules(barcode) {
    if (!/^\d{13}$/.test(barcode)) {
        throw new Error('EAN-13 barcode must contain 13 digits.');
    }

    const digits = [...barcode].map(Number);
    const checksum = digits.slice(0, 12).reduce(
        (sum, digit, index) => sum + digit * (index % 2 === 0 ? 1 : 3),
        0,
    );

    if ((10 - (checksum % 10)) % 10 !== digits[12]) {
        throw new Error('EAN-13 barcode has an invalid check digit.');
    }

    const parity = firstDigitParity[digits[0]];
    let modules = '101';

    for (let index = 1; index <= 6; index++) {
        const pattern = parity[index - 1] === 'O' ? leftOddPatterns : leftEvenPatterns;
        modules += pattern[digits[index]];
    }

    modules += '01010';

    for (let index = 7; index <= 12; index++) {
        modules += rightPatterns[digits[index]];
    }

    return modules + '101';
}

export function renderEan13Barcode(svg, barcode) {
    const modules = getEan13Modules(barcode);
    const namespace = 'http://www.w3.org/2000/svg';
    const quietZone = 11;
    const barHeight = 52;
    const guardHeight = 60;

    svg.replaceChildren();
    svg.setAttribute('viewBox', '0 0 117 72');
    svg.setAttribute('width', '351');
    svg.setAttribute('height', '216');
    svg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
    svg.setAttribute('aria-label', `EAN-13 barcode ${barcode}`);

    for (let index = 0; index < modules.length; index++) {
        if (modules[index] !== '1') {
            continue;
        }

        const isGuard = index < 3 || (index >= 45 && index < 50) || index >= 92;
        const bar = document.createElementNS(namespace, 'rect');
        bar.setAttribute('x', String(quietZone + index));
        bar.setAttribute('y', '0');
        bar.setAttribute('width', '1');
        bar.setAttribute('height', String(isGuard ? guardHeight : barHeight));
        bar.setAttribute('fill', '#000');
        svg.appendChild(bar);
    }

    const text = document.createElementNS(namespace, 'text');
    text.setAttribute('x', '58.5');
    text.setAttribute('y', '70');
    text.setAttribute('text-anchor', 'middle');
    text.setAttribute('font-family', 'monospace');
    text.setAttribute('font-size', '8');
    text.textContent = barcode;
    svg.appendChild(text);
}
