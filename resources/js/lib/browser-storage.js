import _ from "lodash";

export default {
    storage: localStorage,
    set(key, value, merge = false) {
        if (value !== null && (typeof value === 'object' || Array.isArray(value))) {
            if (!Array.isArray(value)) {
                let currentValue = this.getJSON(key);

                if (merge && this.has(key) && _.isObject(currentValue)) {
                    value = _.merge(value, currentValue);
                }
            }

            this.storage.setItem(key, JSON.stringify(value));
        } else {
            this.storage.setItem(key, value);
        }
    },
    get(key, defaultValue = null, parseToJson = false) {
        let value = this.storage.getItem(key);
        const isNested = /^[^.]+(\.[^.]+)+$/.test(key);

        if (typeof value === 'string' && parseToJson || isNested) {
            try {
                value = JSON.parse(value);
                value = isNested ? _.get(value, key) : value;
            } catch (e) {
                //
            }
        }

        return value !== null ? value : defaultValue;
    },
    getJSON(key, defaultValue = null) {
        return this.get(key, defaultValue, true);
    },
    has(key) {
        return this.keys().includes(key);
    },
    remove(key) {
        const value = this.get(key);
        this.storage.removeItem(key);

        return value;
    },
    clearAll() {
        this.storage.clear();
    },
    clearAllExcept(exceptKeys) {
        exceptKeys = typeof exceptKeys === 'string' ? [exceptKeys] : (Array.isArray(exceptKeys) ? exceptKeys : []);

        for (let i = 0; i < this.storage.length; i++) {
            const k = this.storage.key(i);

            if (!exceptKeys.includes(k)) {
                this.remove(k);
            }
        }
    },
    keys() {
        let arr = [];

        for (let i = 0; i < this.storage.length; i++) {
            const k = this.storage.key(i);
            arr.push(k);
        }

        return arr;
    },
}
