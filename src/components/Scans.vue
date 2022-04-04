<template>
  <div class="row row-cols-lg-4 g-3 d-flex justify-content-center text-center mb-3">
    <div v-for="scan in scans" class="col-lg">
      <ScanComponent :scan="scan" @notation="notation"/>
    </div>
  </div>
</template>

<script>
import {mapGetters, mapState} from "vuex";
import Utils from "@/utils";

const ScanComponent = () => import("@/components/ScanComponent");

export default {
  name: 'Scans',
  components: {
    ScanComponent
  },
  props: {
    scans: {}
  },
  computed: {
    ...mapState(['token', 'user']),
  },
  methods: {
    ...mapGetters(['isLogin']),

    async notation({scan, count}) {
      // If the user is not logged, we do not save the notation
      if (!this.isLogin()) {
        return;
      }

      await Utils.put(`api/v1/member/notation/scan`, JSON.stringify({token: this.token, id: scan.id, count: count}), async (success) => {
        if ("error" in success)
          return

        // Refresh episodes
        this.$emit('refresh');

        // If user is null and not have a pseudo, return
        if (!this.user.pseudo)
          return

        await Utils.get(`api/v1/statistics/member/${this.user.pseudo}`, (success) => {
          if ("error" in success)
            return

          this.$store.dispatch('setStatistics', success)
        }, (failed) => null)
      }, (failed) => null)
    },
  }
}
</script>