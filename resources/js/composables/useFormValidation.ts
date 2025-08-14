import { reactive, computed } from 'vue';

export interface FormErrors {
    [key: string]: string[];
}

export interface ValidationRule {
    (value: any): boolean | string;
}

export interface FormField {
    value: any;
    rules: ValidationRule[];
    touched: boolean;
}

export function useFormValidation() {
    const fields = reactive<Record<string, FormField>>({});
    const errors = reactive<FormErrors>({});

    function addField(name: string, initialValue: any = '', rules: ValidationRule[] = []) {
        fields[name] = reactive({
            value: initialValue,
            rules,
            touched: false
        });
    }

    function validateField(name: string): boolean {
        const field = fields[name];
        if (!field) return true;

        const fieldErrors: string[] = [];

        for (const rule of field.rules) {
            const result = rule(field.value);
            if (typeof result === 'string') {
                fieldErrors.push(result);
            } else if (result === false) {
                fieldErrors.push('Invalid value');
            }
        }

        errors[name] = fieldErrors;
        return fieldErrors.length === 0;
    }

    function validateAll(): boolean {
        let isValid = true;

        for (const fieldName in fields) {
            const fieldValid = validateField(fieldName);
            if (!fieldValid) {
                isValid = false;
            }
        }

        return isValid;
    }

    function touchField(name: string) {
        if (fields[name]) {
            fields[name].touched = true;
        }
    }

    function resetForm() {
        for (const fieldName in fields) {
            fields[fieldName].touched = false;
            errors[fieldName] = [];
        }
    }

    function setFieldValue(name: string, value: any) {
        if (fields[name]) {
            fields[name].value = value;
            if (fields[name].touched) {
                validateField(name);
            }
        }
    }

    function getFieldError(name: string): string | null {
        const fieldErrors = errors[name];
        return fieldErrors && fieldErrors.length > 0 ? fieldErrors[0] : null;
    }

    function hasFieldError(name: string): boolean {
        return !!(errors[name] && errors[name].length > 0);
    }

    const isValid = computed(() => {
        return Object.values(errors).every(fieldErrors => fieldErrors.length === 0);
    });

    const hasErrors = computed(() => {
        return Object.values(errors).some(fieldErrors => fieldErrors.length > 0);
    });

    return {
        fields,
        errors,
        addField,
        validateField,
        validateAll,
        touchField,
        resetForm,
        setFieldValue,
        getFieldError,
        hasFieldError,
        isValid,
        hasErrors
    };
}

// Common validation rules
export const validationRules = {
    required: (message = 'This field is required') => (value: any) => {
        if (value === null || value === undefined || value === '') {
            return message;
        }
        return true;
    },

    minLength: (min: number, message?: string) => (value: string) => {
        if (value && value.length < min) {
            return message || `Must be at least ${min} characters long`;
        }
        return true;
    },

    maxLength: (max: number, message?: string) => (value: string) => {
        if (value && value.length > max) {
            return message || `Must be no more than ${max} characters long`;
        }
        return true;
    },

    email: (message = 'Must be a valid email address') => (value: string) => {
        if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            return message;
        }
        return true;
    },

    url: (message = 'Must be a valid URL') => (value: string) => {
        if (value) {
            try {
                new URL(value);
                return true;
            } catch {
                return message;
            }
        }
        return true;
    },

    numeric: (message = 'Must be a number') => (value: any) => {
        if (value !== null && value !== undefined && value !== '' && isNaN(Number(value))) {
            return message;
        }
        return true;
    },

    integer: (message = 'Must be a whole number') => (value: any) => {
        if (value !== null && value !== undefined && value !== '') {
            const num = Number(value);
            if (isNaN(num) || !Number.isInteger(num)) {
                return message;
            }
        }
        return true;
    },

    min: (min: number, message?: string) => (value: any) => {
        const num = Number(value);
        if (!isNaN(num) && num < min) {
            return message || `Must be at least ${min}`;
        }
        return true;
    },

    max: (max: number, message?: string) => (value: any) => {
        const num = Number(value);
        if (!isNaN(num) && num > max) {
            return message || `Must be no more than ${max}`;
        }
        return true;
    }
};
