<template>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <CardEpisodeHeader :current-episode="episode"/>
        <CardEpisodeImage :current-episode="episode"/>
        <CardEpisodeFooter :current-episode="episode"/>
      </div>
    </div>
  </div>
</template>

<script>
import CardEpisodeInfo from "@/components/CardEpisodeInfo";
import CardEpisodeImage from "@/components/CardEpisodeImage";
import CardEpisodeFooter from "@/components/CardEpisodeFooter";
import CardEpisodeHeader from "@/components/CardEpisodeHeader";

export default {
  name: "CardEpisode",
  components: {CardEpisodeHeader, CardEpisodeFooter, CardEpisodeImage, CardEpisodeInfo},
  props: {
    episode: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      interval: null,
    }
  },
}
</script>