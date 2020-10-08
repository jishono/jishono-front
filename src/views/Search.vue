<template>
  <div>
    <div
      id="inputfield"
      style="min-height: 10px;"
    >
    </div>
    <div class='row'>
      <div class='col-xs-2 col-lg-3'></div>
      <div
        class='col-xs-8 col-lg-6 mx-auto'
        style="max-width: 600px"
      >
        <div class='row mx-md-5'>
          <div class="col">
            <div class='input-group '>
              <input
                :value="q"
                v-on:input="q = $event.target.value"
                ref="search"
                autofocus
                id="search"
                class="input-lg form-control"
                placeholder='ノルウェー語の検索ワード'
                type="text"
                v-bind:class="{'form-control': true, 'error': !valid }"
              >
              <button
                v-if="this.q != ''"
                class="btn bg-transparent"
                style="margin-left: -40px; z-index: 100; "
                @click="q = ''"
                tabindex="1"
              >
                <i class="fa fa-times"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="row mt-2 mx-md-5">
          <div class="col-9">
            <span
              v-show="!valid"
              class="text-danger"
            >半角英数字で入力して下さい。</span>
            <span v-if="suggestions.length > 0">
              もしかして ->
              <span
                v-for="(suggestion, i) in suggestions"
                :key=suggestion.oppslag
              >
                <span
                  style="cursor: pointer; text-decoration: underline;"
                  @click="searchSuggestion(suggestion.oppslag)"
                  @keyup.enter="searchSuggestion(suggestion.oppslag)"
                  tabindex="0"
                >
                  {{ suggestion.oppslag}}</span>
                <span v-if="i != suggestions.length-1">,&nbsp;</span>
              </span>
            </span>
          </div>
          <div class="col-3 text-right">
            <span
              v-if="q.length > 0"
              class="text-right"
            >
              {{filteredResults.length}} 件</span>
          </div>
        </div>

        <div
          v-for="(result,index) in filteredResults"
          :key="result.lemma_id"
          class='row my-2'
        >
          <div class="col px-2">
            <ResultBox
              v-bind:word="result"
              @get-example-sentences="getExampleSentences(result.lemma_id, index)"
              @get-conjugations="openModal(result.lemma_id, result.oppslag, result.boy_tabell)"
              @kanren-click="handleKanrenClick"
              @feedback-click="openFeedbackDialog(result.lemma_id, result.oppslag, result.definisjoner)"
            />
          </div>
        </div>
      </div>
      <div class='col-xs-2 col-lg-3'></div>
    </div>
    <ConjugationModal
      v-bind:show="showDialog"
      v-bind:wordID="currentWordID"
      v-bind:pos="currentpos"
      v-bind:word="currentWord"
      v-on:close="closeModal"
    ></ConjugationModal>
    <transition
      name="fade"
      appear
    >
      <div
        class="modal-overlay"
        v-if="showFeedbackDialog"
        @click.self="showFeedbackDialog = false"
      >
        <div class="vipps-modal">
          <div class="text-right mb-2">
            <span
              class="float-left"
              style="font-weight: bold"
            >フィードバック</span>
            <button
              class="btn bg-transparent p-0"
              style="margin-top: -16px"
              @click="showFeedbackDialog = false"
            >
              <i class="fa fa-times"></i>
            </button>
          </div>
          <div>単語：{{currentWord}}</div>
          <div
            v-for="def in this.currentDefinitions"
            :key="def.def_id"
          >
            <span>{{def.prioritet}}. {{def.definisjon}}</span>
          </div>
          <p class="mt-3"></p>
          <textarea
            v-model="feedback"
            class="form-control mt-6"
            rows="5"
            maxlength="500"
            placeholder="訳や例文に誤訳、誤字などがありましたらお知らせください"
          ></textarea>
          <div class="row no-gutters mt-2">
            <div class="col d-flex justify-content-between align-items-center">
              <span class="donate-text ">
                辞書作りに興味ありますか？
                <a
                  href="https://baksida.jisho.no/om"
                  target="_blank"
                >Baksida</a>でjisho.noを改善していきましょう！
              </span>
              <button
                class="btn btn-sm shadow-none btn-outline-primary ml-2"
                type="button"
                @click="sendFeedback"
                style="min-width: 50px"
              >
                <span>
                  送信
                </span>
              </button>

            </div>
          </div>
          <div class="row no-gutters">
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import api from '../api.js'
import ConjugationModal from '../components/ConjugationModal'
import ResultBox from '../components/ResultBox'

export default {
  name: "Search",
  components: {
    ConjugationModal, ResultBox
  },
  data () {
    return {
      suggestions: [],
      results: [],
      q: '',
      valid: true,
      showDialog: false,
      showFeedbackDialog: false,
      feedback: "",
      currentWordID: null,
      currentWord: null,
      currentDefinitions: null,
      currentpos: null,

    }
  },
  methods: {
    getSuggestionList () {
      api.get('/suggestion_list?q=' + this.q)
        .then(response => {
          this.suggestions = response.data
        })
    },
    getAllItems () {
      api.get('/items/all')
        .then(response => {
          this.results = response.data
        })
    },
    validate () {
      if (this.q.length === 0) {
        return true
      }
      const matches = this.q.match(/^[0-9A-ZÆÅØa-zæåø\s.-]+$/)
      return matches ? true : false
    },
    getExampleSentences (lemma_id, index) {
      if (!this.filteredResults[index].example_sentences) {
        api.get('/example_sentences/' + lemma_id)
          .then(response => {
            this.$set(this.filteredResults[index], 'example_sentences', response.data)
          })
      }
    },
    searchSuggestion (suggestion) {
      this.q = suggestion
      this.$nextTick(() => {
        this.$refs.search.focus({
          preventScroll: true
        })
      })
    },
    handleKanrenClick (query) {
      this.q = query
    },
    openFeedbackDialog (wordID, word, definitions) {
      this.showFeedbackDialog = true
      this.currentWordID = wordID
      this.currentWord = word
      this.currentDefinitions = definitions
      this.feedback = ""
    },
    closeFeedbackDialog () {
      this.showFeedbackDialog = false
      this.currentWordID = null
      this.currentWord = null
      this.currentDefinitions = null
      this.feedback = ""
    },
    sendFeedback () {
      api.post('/feedback', {
        wordID: this.currentWordID,
        feedback: this.feedback
      })
        .then(() => {
          this.closeFeedbackDialog()
        })
    },
    openModal (wordID, word, pos) {
      this.showDialog = true
      this.currentWordID = wordID
      this.currentpos = pos
      this.currentWord = word
      document.documentElement.style.overflow = 'hidden'
    },
    closeModal () {
      this.showDialog = false
      this.currentWordID = null
      this.currentpos = null
      this.currentWord = null
      document.documentElement.style.overflow = 'auto'
    },


  },
  computed: {
    filteredResults () {
      let filtered = []
      if (this.q.length > 0) {
        filtered = this.results
          .filter(item => {
            if (item.oppslag.toLowerCase().includes(this.q)) {
              return true
            }
          })
          .sort((a, b) => {
            if (a.oppslag.toLowerCase().indexOf(this.q) > b.oppslag.toLowerCase().indexOf(this.q)) {
              return 1
            } else if (a.oppslag.toLowerCase().indexOf(this.q) < b.oppslag.toLowerCase().indexOf(this.q)) {
              return -1
            } else {
              if (a.oppslag.toLowerCase() > b.oppslag.toLowerCase()) {
                return 1
              } else {
                return -1
              }
            }

          })
          .slice(0, 30)
      }
      return filtered
    },
  },
  created () {
    this.getAllItems()
    this.q = this.$route.params.query || ''
  },
  watch: {
    q: function (val) {
      this.valid = this.validate()
      var scrollElement
      if (this.valid && this.q.length > 0) {
        this.q = val.toLowerCase()
        this.$router.push('/search/' + val, () => { })
        this.getSuggestionList()
      }
      if (val != '' && window.screen.width < 600) {
        scrollElement = document.getElementById("inputfield");
        scrollElement.scrollIntoView();
      } else {
        window.scrollTo(0, 0);
      }
    },
    $route (to) {
      if (to.path === '/search/') {
        this.q = ''
      }
    }
  }
}
</script>