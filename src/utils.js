export default class Utils {
    static getLocalFile(path) {
        return window.location.protocol + "//" + window.location.hostname + "/" + path
    }

    static getFile(path) {
        return window.location.protocol + "//" + window.location.hostname + ":" + window.location.port + "/" + path
    }

    static toHHMMSS(string) {
        const sec_num = parseInt(string, 10)

        if (sec_num <= 0)
            return '??:??'

        let hours = Math.floor(sec_num / 3600)
        let minutes = Math.floor((sec_num - (hours * 3600)) / 60)
        let seconds = sec_num - (hours * 3600) - (minutes * 60)

        const options = {minimumIntegerDigits: 2}
        return (hours >= 1 ? hours.toLocaleString('fr-FR', options) + ':' : '') + minutes.toLocaleString('fr-FR', options) + ':' + seconds.toLocaleString('fr-FR', options)
    }

    static timeSince(time) {
        const seconds = Math.floor((new Date().getTime() - time) / 1000)
        return Utils.getTimeFormat(seconds)
    }

    static getTimeFormat(seconds) {
        let interval = seconds / 31536000
        if (interval > 1) return Math.floor(interval) + " an" + (interval >= 2 ? "s" : "")
        interval = seconds / 2592000
        if (interval > 1) return Math.floor(interval) + " mois"
        interval = seconds / 86400
        if (interval > 1) return Math.floor(interval) + " jour" + (interval >= 2 ? "s" : "")
        interval = seconds / 3600
        if (interval > 1) return Math.floor(interval) + " heure" + (interval >= 2 ? "s" : "")
        interval = seconds / 60
        if (interval > 1) return Math.floor(interval) + " minute" + (interval >= 2 ? "s" : "")
        return Math.floor(seconds) + " seconde" + (seconds >= 2 ? "s" : "")
    }

    static isNullOrEmpty(payload) {
        return (payload === undefined || payload === null) || (payload.length <= 0)
    }

    static isNotNull(payload) {
        return !Utils.isNullOrEmpty(payload)
    }

    static getInData(object, key, defaultValue) {
        return Utils.isNotNull(object) ? (Utils.isNotNull(object[key]) ? object[key] : defaultValue) : defaultValue
    }

    static async get(url, onSuccess, onFailed) {
        try {
            const response = await fetch(Utils.getLocalFile(url))
            const json = await response.json()
            onSuccess(json)
        } catch (exception) {
            onFailed(exception)
        }
    }

    static async post(url, body, onSuccess, onFailed) {
        try {
            const response = await fetch(Utils.getLocalFile(url), {
                method: 'POST',
                body: body,
                headers: {
                    "Content-Type": "application/json"
                }
            })

            const json = await response.json()
            onSuccess(json)
        } catch (exception) {
            onFailed(exception)
        }
    }
}