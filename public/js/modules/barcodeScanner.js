"use strict";

/**
 * Initializes the barcode scanner.
 *
 * Listens for keypress events on the entire document,
 * and intercepts keys if the first key pressed is '@'.
 * If the Enter key is pressed, the barcode is processed.
 * If not, a timeout is set to process the barcode after 100ms.
 *
 * @param {string} [targetInputSelector='#barcode'] - The target input element to focus on after processing the barcode.
 */
export function initBarcodeScanner(targetInputSelector = '#barcode') {
    
    let barcode = "";
    let first_key = "";
    let last_key = "";
    let timer;
    let scanning = false;

    const $barcodeInput = $(targetInputSelector);

    if ($barcodeInput.length === 0) {
        console.warn(`Barcode Scanner: target input "${targetInputSelector}" not found.`);
        return;
    }

    $(document).on('keypress', function (e) {
        const $focused = $(document.activeElement);

        if (timer) clearTimeout(timer);

        // Detect first key
        if (barcode.length === 0) {
            first_key = e.key;
            if (first_key === "@") {
                scanning = true;
                barcode = "@";
                e.preventDefault(); // prevent @ from reaching any input
                return;
            }
        }

        // If scanning, intercept keys
        if (scanning) {
            e.preventDefault(); // block keys from focused input
            barcode += e.key;
            last_key = e.key;

            if (e.key === "Enter") {
                processBarcode();
                return;
            }

            // timeout in case Enter isn't detected
            timer = setTimeout(processBarcode, 100);
        }
    });


    /**
     * Process the barcode string.
     * If the string starts with '@' and ends with 'Enter', it is considered a valid barcode.
     * Otherwise, the input is ignored.
     * Resets the barcode string and keys after processing.
     */
    function processBarcode() {
        if (!scanning) return;
        scanning = false;

        if (first_key === "@" && last_key === "Enter") {
            // Remove '@' and Enter from the string
            const cleanBarcode = barcode.slice(1, -5);
            $barcodeInput.val(cleanBarcode);
            $barcodeInput.trigger("change" )

            console.log("✅ Barcode scanned:", cleanBarcode);
            
        } else {
         //   console.log("⛔ Ignored input:", barcode);
        }

        // Reset
        barcode = "";
        first_key = "";
        last_key = "";
    }
}
