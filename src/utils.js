export default class Utils {
    static getLocalFile(path) {
        return window.location.protocol + "//" + window.location.hostname + "/" + path;
    }

    static getFile(path) {
        return window.location.protocol + "//" + window.location.host + "/" + path;
    }

    static toHHMMSS(string) {
        const sec_num = parseInt(string, 10); // don't forget the second param
        let hours = Math.floor(sec_num / 3600);
        let minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        let seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (hours < 10) {
            hours = "0" + hours;
        }
        if (minutes < 10) {
            minutes = "0" + minutes;
        }
        if (seconds < 10) {
            seconds = "0" + seconds;
        }

        return (hours >= 1 ? hours + ':' : '') + minutes + ':' + seconds;
    }

    static timeSince(time) {
        const seconds = Math.floor((new Date().getTime() - time) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " an" + (interval >= 2 ? "s" : "");
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " mois";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " jour" + (interval >= 2 ? "s" : "");
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " heure" + (interval >= 2 ? "s" : "");
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " minute" + (interval >= 2 ? "s" : "");
        return Math.floor(seconds) + " seconde" + (seconds >= 2 ? "s" : "");
    }

    static isNull(payload) {
        return payload === undefined || payload === null
    }

    static isNotNull(payload) {
        return payload !== undefined && payload !== null
    }

    static getInData(object, key, defaultValue) {
        return Utils.isNotNull(object) ? (Utils.isNotNull(object[key]) ? object[key] : defaultValue) : defaultValue
    }
}