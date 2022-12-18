function validateAlias(alias) {
    
    if (alias == '') {
        return 'empty';
    }
    
    var testCase = "";
    var aliasError = "";

    if (alias.indexOf("@") == -1) {
        testCase = checkAndFormatNumber(alias);
    } else if (alias.indexOf("@") > -1) {
        aliasError = !alias.match(/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/g);
    }
 	
    if (testCase == 'invalid' || aliasError) {
        return 'invalid';
	}
    
    return 'valid';
}

function checkAndFormatNumber(phoneNumber) {
    phoneNumber = phoneNumber.split("(").join("");
    phoneNumber = phoneNumber.split(")").join("");
    phoneNumber = phoneNumber.split("-").join("");

    if (phoneNumber.match(/\d{13}/g) && phoneNumber.substring(0, 5) == "00386") {
        return phoneNumber;
    } else if (phoneNumber.match(/\+\d{11}/g) && phoneNumber.substring(0, 4) == "+386") {
        return phoneNumber.replace("+", "00");
    } else if (phoneNumber.match(/\d{11}/g) && phoneNumber.substring(0, 3) == "386") {
        return "00" + phoneNumber;
    } else if (phoneNumber.match(/\d{9}/g) && phoneNumber[0] == "0") {
        return "00386" + phoneNumber.substring(1, 9);
    } else {
        return "invalid";
    }
}