"use strict";


const SFNumberFunctions = {
    // Translation js trait

    // Converts any number of any format to a user-selected formated number ...
    _displayNumber: ( number , digits = 0)      => {

        // Database decimal strings use a dot, while formatted form values use a comma.
        if (typeof number === "string" && !number.includes(",") && /^-?\d+\.\d+$/.test(number)) {
            number = parseFloat(number);
        }

        // transform to a valid number first
        number = SFNumberFunctions._parseNumber ( number );
        

        if ( number == 0 ) { return "0,00"; }
        if ( number == null || isNaN( number ) ) { return null; }
        
        // Round to 2 decimal places
        if ( digits == 2 ) { number = Math.round(number * 100) / 100; }
        else if ( digits == 3 ) { number = Math.round(number * 1000) / 1000; }

        const strNumber = String ( number );
        let parts       = strNumber.indexOf(".") >= 0 ? strNumber.split(".") :  number.toFixed (2).split( "." );

        parts[0] = parts[0].replace(",", "."); // Replace thousand separators ..
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Add to every 3 numbers ...

        return strNumber.indexOf(".") >= 0 ? parts.join(',') : parts[0] + ',00' ;
    },

    _displayCurrency: ( number, digits = 0 )    => {

        number =  SFNumberFunctions._displayNumber ( number , digits );
        if ( number == null ) { return number; }

        return number + " " + "€";
        

    },

    _displayPercent : ( number )    => {
        number =  SFNumberFunctions._displayNumber ( number );
        if ( number == null ) { return number; }
        return number + " " + "%";
    },

    _calculateMarkup : ( number1, number2 ) => {
        return SFNumberFunctions._parseNumber ( number1 ) / SFNumberFunctions._parseNumber ( number2 );
    },

    _calculateNumWithMarkup : ( number, markup ) => {
        return SFNumberFunctions._parseNumber ( number ) * SFNumberFunctions._parseNumber ( markup );
    },

    _calculateNumberPercentage : ( number, percentage ) => {
        number = SFNumberFunctions._parseNumber ( number );
        percentage = number * SFNumberFunctions._parseNumber ( percentage ) / 100;
        
        return percentage.toFixed( 2 ) ;
    },

    


    // Whatever happens, this fucntion always returns a valid float number for manipulation...
    _parseNumber: ( number )        => {

        if ( !number  ) return 0.00;
		
		// already a number
		if (typeof number === "number") { return number; }
		
		const str = String(number).trim();
		return parseFloat(str.replace(/\./g, '').replace(',', '.'));
    },

    _setInputNumber : ( selector , number) => {
		Inputmask.setValue( selector[0], SFNumberFunctions._displayNumber ( number )  );
    },

    _initInputFormat : ( selector = ".number-input" ) => {
		document.querySelectorAll(selector).forEach((element) => {
		    if (element.inputmask) return;

		    const options = {
		        alias: "numeric",
		        groupSeparator: ".",
		        autoGroup: true,
		        digits: 2,
		        digitsOptional: true,
		        radixPoint: ",",
		        substitutes: {},
		        placeholder: "0",
		        rightAlign: false,
		        inputmode: "decimal",
		    };

		    if (element.hasAttribute("data-min")) {
		        options.min = Number(element.getAttribute("data-min"));
		    }
		    if (element.hasAttribute("data-max")) {
		        options.max = Number(element.getAttribute("data-max"));
		    }

		    element.addEventListener("keydown", (event) => {
		        if (event.key === ".") {
		            event.preventDefault();
		            event.stopImmediatePropagation();
		        }
		    }, true);

		    Inputmask(options).mask(element);
		});
    }

};
