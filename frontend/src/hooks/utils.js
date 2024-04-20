const Utils = () => {

    const formatPhoneNumber = input => {
        if (!input) return input;
        const numberInput = input.replace(/[^\d]/g, "");
        const numberInputLength = numberInput.length;
    
        if (numberInputLength < 4) {
            return numberInput;
        } else if (numberInputLength < 7) {
            return `(${numberInput.slice(0, 3)}) ${numberInput.slice(3)}`;
        } else if (numberInputLength < 11) {
            return `(${numberInput.slice(0, 2)}) ${numberInput.slice(2, 6)}-${numberInput.slice(6, 10)}`;
        } else {
            return `(${numberInput.slice(0, 2)}) ${numberInput.slice(2, 7)}-${numberInput.slice(7, 11)}`;
        }
    }

    const getActivityLevel = (value) => {
        switch (value) {
            case 0.2:
                return 'No sport/exercise';
            case 0.375:
                return 'Light activity (sport 1-3 times per week)';
            case 0.55:
                return 'Moderate activity (sport 3-5 times per week)';
            case 0.725:
                return 'High activity (everyday exercise)';
            case 0.9:
                return 'Extreme activity (professional athlete)';
            default:
                return '';
        }
    }

    return {
        formatPhoneNumber: formatPhoneNumber,
        getActivityLevel: getActivityLevel
    }
}
export default Utils