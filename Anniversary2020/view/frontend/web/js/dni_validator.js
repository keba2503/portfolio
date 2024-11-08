require(['jquery', 'jquery/validate'], function ($) {

    $.validator.addMethod("dnivalidator", function (value, element) {
        let dni = value.toUpperCase();
        let dniRegex = /^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/g;
        let nieRegex = /^[XYZ][0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKE]$/g;
        let passportRegex = /^[A-Z]{3}[0-9]{6}$/g;
        let cifRegex = /^[ABCDEFGHJKLMNPQRSUVW][0-9]{7}[0-9A-J]$/g;

        return (dniRegex.test(dni) && dniCharIsCorrect(dni)) || (nieRegex.test(dni) && nieCharIsCorrect(dni)) || passportRegex.test(dni) || (cifRegex.test(dni) && cifCharIsCorrect(dni));
    }, "Debe introducir un dni válido");

    function dniCharIsCorrect(dni) {
        let dniNumber = dni.substr(0, dni.length - 1);
        let dniChar = dni.substr(dni.length - 1, dni.length);
        let dniLetters = "TRWAGMYFPDXBNJZSQVHLCKET";
        let pos = dniNumber % 23;
        let dniRealChar = dniLetters.substring(pos, pos + 1);

        return dniChar === dniRealChar;
    }

    function nieCharIsCorrect(nie) {
        let nieFirstChar = nie.substr(0, 1);
        let nieCharToNumber = nieFirstChar.replace('X', '0');
        nieCharToNumber = nieCharToNumber.replace('Y', '1');
        nieCharToNumber = nieCharToNumber.replace('Z', '2');
        let nieNumber = nieCharToNumber + nie.substr(1, nie.length - 2);
        let nieChar = nie.substr(nie.length - 1, nie.length);

        let nieLetters = "TRWAGMYFPDXBNJZSQVHLCKET";
        let pos = nieNumber % 23;
        let nieRealChar = nieLetters.substring(pos, pos + 1);

        return nieChar === nieRealChar;
    }

    function cifCharIsCorrect(cif) {
        let cifCharsToNumberResult = "ABCDEFGHJUV";

        let nieFirstChar = cif.substr(0, 1);
        let cifNumber = cif.substr(1, cif.length - 2);
        let cifControlChar = cif.substr(cif.length - 1, cif.length);

        let evenSum = getDigitsSum(cifNumber, 0);
        let oddSum = getDigitsSum(cifNumber, 1);
        let totalSum = evenSum + oddSum;
        let cifRealChar = 10 - (totalSum % 10);
        let numberResult = cifCharsToNumberResult.indexOf(nieFirstChar);
        cifRealChar = numberResult >= 0 ? cifRealChar + "" : numberToLetters(cifRealChar);
        cifRealChar = cifRealChar === "10" ? "0" : cifRealChar;

        return cifControlChar === cifRealChar;
    }

    function numberToLetters(num) {
        return String.fromCharCode(num + 64);
    }

    function reverse(number) {
        let originalNumber = number;
        let zeroCount = 0;
        while (number.substr(number.length - 1) === "0") {
            zeroCount++;
            number = number.substr(0, number.length - 1);
        }
        let reverse = 0;
        while (number !== 0) {
            reverse = (reverse * 10) + (number % 10);
            number = Math.floor(number / 10);
        }
        while (zeroCount > 0) {
            reverse = "0" + reverse;
            zeroCount--;
        }
        while (originalNumber.length !== reverse.toString().length) {
            reverse = reverse + "0";
        }
        return reverse;
    }

    function getDigitsSum(number, odd) {
        number = reverse(number);
        let sumOdd = 0, sumEven = 0, counter = 1, digit = 0;

        while (number !== 0) {
            if (counter % 2 === 0) {
                sumEven += number % 10;
            } else {
                digit = (number % 10) * 2;
                sumOdd += (digit % 10) + Math.floor(digit / 10);
            }
            number = Math.floor(number / 10);
            counter++;
        }

        return odd ? sumOdd : sumEven;
    }

    return this;
});
