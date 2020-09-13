<template>
  <footer>
    <div class="container">
      <div class="row justify-content-center">
        <div
          class='text-center my-4 no mx-3'
          style="max-width: 400px; width: 100%;"
        >

          <div
            @click="showModal = true"            
            style="cursor: pointer"
            class="hover-glow"
          >
            <div class="d-flex justify-content-between donate-text">

              <span>寄付はVippsで</span>
              <span>2020年の募金目標額</span>
            </div>
            <div
              class="progress "
              style="height: 20px;"
            >
              <div
                class="progress-bar "
                role="progressbar"
                :style="'width:' + progress + '%; min-width: 70px;'"
              ><span>{{collected}} / {{goal}}kr</span>
              </div>
            </div>
          </div>
          <div
            class="my-3"
            style="font-size: 16px"
          >

           
            <div class="my-1">
               <router-link
              class="badge badge-pill pill-button mx-1"
              to="/about"
            >jisho.noについて</router-link>
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
        </div>
      </div>
    </div>
    <div style="height: 800px">

    </div>
    <transition
      name="fade"
      appear
    >
      <div
        class="modal-overlay"
        v-if="showModal"
        @click="showModal = false"
      >
        <div class="vipps-modal">
          <div class="text-right mb-2">
            <span class="float-left" style="font-weight: bold">jisho.noの募金運動</span>
            <button
              class="btn bg-transparent p-0" style="margin-top: -16px"
              @click="showModal = false"
            >
              <i class="fa fa-times"></i>
            </button>
          </div>
          <p>jisho.noは100%ボランティア・非営利のプロジェクトであるため、
            サーバーやドメイン名への支払いはユーザーの方々からのサポートにかかっています。
            規模の大小を問わず全ての寄付をお受けし、全額をプロジェクトそのものに対して使用します。
          </p>
          <p>
            恐れ入りますが、今のところVippsでのお支払いのみ受け付けております。
          </p>
          <img
            class="img-fluid text-center mt-4"
            src='@/assets/vipps.png'
          >

        </div>
      </div>
    </transition>
  </footer>
</template>

<script>
import api from '../api.js'

export default {
  name: 'Footer',
  data () {
    return {
      collected: 45,
      goal: 1200,
      showModal: false,
    }
  },
  computed: {
    progress () {
      return this.collected / this.goal * 100
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