<template>
  <div class='container-fluid'>
    <div
      class='row'
      id='header'
    >
      <div class='col'>
        <img
          class='logo my-4'
          src='@/assets/jisho_logo_beta.png'
          @click="q = ''"
          style="cursor: pointer;"
        />
      </div>
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
                v-model="q"
                ref="search"
                autofocus
                name="search"
                class="input-lg form-control"
                placeholder='ノルウェー語の検索ワード'
                type="text"
                v-bind:class="{'form-control': true, 'error': !valid }"
              >
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
          v-for="result in filteredResults"
          :key="result.lemma_id"
          class='row my-2'
        >
          <div class="col px-2">
            <div class='card my-2'>
              <div class='card-header py-1'>
                <div class='row'>
                  <div class=' col-md-4 text-center kotoba my-auto no'>{{ result.oppslag }}</div>
                  <div class='col-6 col-md-4 text-md-center text-left m-auto'>
                    <span class="hatsuon ja">発音:</span> <span
                      class='no'
                      v-if="result.uttale.length > 0"
                    >{{result.uttale[0].transkripsjon}}</span></div>
                  <div class='col-6 col-md-4 text-md-center text-right hinshi m-auto ja'>
                    <span @click="openModal(result.lemma_id, result.oppslag, result.boy_tabell)">{{ partsOfSpeech[result.boy_tabell]}}
                      <span v-if="result.boy_tabell === 'subst'">{{ getGender(result.pos)}}
                      </span></span>
                  </div>
                </div>
              </div>
              <div class='imi my-3 ja'>
                <div
                  v-for="def in result.definisjoner"
                  :key="def.def_id + def.definisjon"
                >
                  <span class='imi-count ml-4 mr-2'>{{def.prioritet}}.</span>
                  <span
                    v-html="addFurigana(def.definisjon)"
                    class='imi'
                  ></span><br>
                </div>
              </div>
            </div>
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
  </div>

</template>

<script>
import api from '../api.js'
import ConjugationModal from './ConjugationModal'

export default {
  name: "Search",
  components: {
    ConjugationModal
  },
  data () {
    return {
      suggestions: [],
      results: [],
      q: '',
      valid: true,
      showDialog: false,
      currentWordID: null,
      currentWord: null,
      currentpos: null,
      partsOfSpeech: {
        verb: "動詞",
        adj: "形容詞",
        preposisjon: "前置詞",
        interjeksjon: "感動詞",
        prefiks: "接頭辞",
        adv: "副詞",
        det: "限定詞",
        subjunksjon: "関係詞",
        pron: "代名詞",
        konjunksjon: "接続詞",
        subst: "名詞"
      },
    }
  },
  methods: {
    getSuggestionList () {
      api.get('/suggestion_list?q=' + this.q)
        .then(response => {
          this.suggestions = response.data
        })
    },
    getSearchResults () {
      api.get('/search?q=' + this.q)
        .then(response => {
          this.results = response.data
        })
    },
    validate () {
      if (this.q.length === 0) {
        return true
      }
      const matches = this.q.match(/^[0-9A-ZÆÅØa-zæåø\s.]+$/)
      return matches ? true : false
    },
    searchSuggestion (suggestion) {
      this.q = suggestion
      this.$nextTick(() => this.$refs.search.focus())
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
    getGender (posArray) {
      let pos
      for (pos of posArray) {
        if (pos.includes('f')) {
          return '(女)'
        }
      }
      for (pos of posArray) {
        if (pos.includes('n')) {
          return '(中)'
        }
        if (pos.includes('m')) {
          return '(男)'
        }
      }
      return ''
    },
    addRuby (str) {
      const kanji = str.match(/.*?(?=\[)/)
      const furigana = str.match(/(?<=\[).*(?=\])/)
      return `<ruby>${kanji}<rp>(</rp><rt>${furigana}</rt><rp>)</rp></ruby>`
    },
    addFurigana (str) {
      if (!str.match(/\[/)) {
        return str
      }
      const regex = new RegExp(/[^）、]*?\]/g)
      const matches = str.match(regex)
      let final_string = str
      if (matches) {
        matches.forEach(match => {
          final_string = final_string.replace(match, this.addRuby(match))
        })
      }
      return final_string
    }
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
  mounted () {
    this.getSearchResults()
    this.q = this.$route.params.query || ''
  },
  watch: {
    q: function (val) {
      this.valid = this.validate()
      if (this.valid) {
        this.q = val.toLowerCase()
        this.$router.push('/search/' + val, () => { })
        this.getSuggestionList()
      }
    },
    /*    filteredResults (filteredArray) {
         this.suggestions = []
         if (this.q.length > 1 && filteredArray.length === 0) {
           this.getSuggestionList()
         }
       } */
  }
}
</script>