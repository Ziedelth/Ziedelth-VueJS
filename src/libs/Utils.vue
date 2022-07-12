<script>
import Const from "@/const";

export default {
  name: "Utils",
  methods: {
    getAttachment(src) {
      if (src == null || src.startsWith("http")) {
        return src;
      }

      return Const.ATTACHMENTS_URL + src;
    },
    joinGenres() {
      return this.anime.genres.map(genre => genre.fr).join(", ");
    },
    toTitle() {
      const string = this.episode.title

      if (string == null || string === "") {
        return "＞﹏＜";
      }

      return string
    },
    toDescription() {
      return this.episode.anime.country.season + " " + this.episode.season + " • " + this.episode.episodeType.fr + " " + this.episode.number + " " + this.episode.langType.fr
    },
    toHHMMSS() {
      const sec_num = parseInt(this.episode.duration, 10)

      if (sec_num <= 0)
        return '??:??'

      let hours = Math.floor(sec_num / 3600)
      let minutes = Math.floor((sec_num - (hours * 3600)) / 60)
      let seconds = sec_num - (hours * 3600) - (minutes * 60)

      const options = {minimumIntegerDigits: 2}
      return (hours >= 1 ? hours.toLocaleString('fr-FR', options) + ':' : '') + minutes.toLocaleString('fr-FR', options) + ':' + seconds.toLocaleString('fr-FR', options)
    },
    toTimeSince() {
      const seconds = Math.floor((new Date().getTime() - new Date(this.episode.releaseDate).getTime()) / 1000)
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
  }
}
</script>