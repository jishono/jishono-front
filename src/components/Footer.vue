<template>
  <footer>
    <div class="container">
      <div class="row justify-content-center">
        <div
          class='text-center my-4 no mx-3'
          style="max-width: 400px; width: 100%;"
        >
          <div
            @click="showVippsDialog = true"
            class="hover-glow pointer"
          >
            <div class="d-flex justify-content-between donate-text">
              <span>{{ $t('footer.vipps') }}</span>
              <span>{{ $t('footer.goal') }}</span>
            </div>
            <div
              class="progress "
              style="height: 20px;"
            >
              <div
                class="progress-bar "
                role="progressbar"
                :style="'width:' + progress + '%; min-width: 80px;'"
              ><span>{{collected}} / {{goal}}kr</span>
              </div>
            </div>
          </div>
          <div
            class="mt-3 oneliner"
          >
            {{ $t('footer.oneliner') }}
          </div>
          <div
            class="my-2"
          >
            <div class="my-1">
              <router-link
                class="badge badge-pill pill-button mx-1"
                to="/about"
              >{{ $t('footer.about') }}</router-link>
              <a
                href="https://github.com/jishono/"
                target="_blank"
                class="badge badge-pill pill-button mx-1"
              >Github</a>
              <a
                href="https://baksida.jisho.no/om"
                target="_blank"
                class="badge badge-pill pill-button mx-1"
              >Baksida</a>
            </div>
          </div>
          <div class="mt-3">
            <img
              class="flag-icon mx-1"
              height="25px"
              width="25px"
              src="@/assets/japan-icon-border.png"
              @click="setLocale('ja')"
            >
            <img
              class="flag-icon mx-1"
              height="25px"
              width="25px"
              bor
              src="@/assets/norway-icon.png"
              @click="setLocale('no')"
            >
          </div>
        </div>
      </div>
    </div>
    <div style="
              height:
              800px">
    </div>
    <VippsDialog
      v-bind:show="showVippsDialog"
      @close-vipps-dialog="showVippsDialog = false"
    />
  </footer>
</template>

<script>
import api from '../api.js'
import VippsDialog from '../components/VippsDialog'

export default {
  name: 'Footer',
  components: { VippsDialog },
  data () {
    return {
      collected: 45,
      goal: 1200,
      showVippsDialog: false,
    }
  },
  computed: {
    progress () {
      return this.collected / this.goal * 100
    }
  },
  methods: {
    setLocale (locale) {
      this.$i18n.locale = locale
      localStorage.setItem('locale', locale)
    }
  },
  mounted () {
    api.get('https://spreadsheets.google.com/feeds/list/1932p6AND-EnBwZ9ST5lfDn9nTD42MaVwsTVPzugplrQ/1/public/values?alt=json')
      .then(response => {
        this.collected = response.data.feed.entry[0].gsx$innsamlet.$t
        this.goal = response.data.feed.entry[0].gsx$mål.$t
      })
  }

}

</script>