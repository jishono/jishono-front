<template>
  <div class='container-fluid'>
    <div
      class='row'
      id='header'
    >
      <div class='col'>
        <a href='/'>
          <img
            class='logo my-4'
            src='@/assets/jisho_logo_beta.png'
          />
        </a>
      </div>
    </div>
    <div
      class='row'
      id='searchbar'
    >
      <div class='col-lg'></div>
      <div class='input-group col'>
        <input
          v-model="q"
          autofocus
          class="input-lg form-control"
          placeholder='検索ワード (半角英数字)'
          type="text"
          title="半角英数字で入力して下さい。"
        >
      </div>
      <div class='col-lg'></div>
    </div>
    <div
      class='row my-4 ja'
      id='number'
    >
      <div v-if="q.length > 1" class='col text-center'>該当件数: {{filteredResults.length}}</div>
    </div>
    <span>もしかして...</span>
    <span v-for="suggestion in suggestions" :key=suggestion.oppslag>
      {{ suggestion.oppslag }}, 
    </span>
    <div id='result-container'>
      <div
        v-for="result in filteredResults"
        :key="result.lemma_id"
        class='row my-2'
      >
        <div class='col-lg-2 col-xl-3'></div>
        <div class='col'>
          <div class='card my-2'>
            <div class='card-header py-1'>
              <div class='result-top-container'>
                <div class='row'>
                  <div class=' col-md-4 text-center kotoba my-auto no'>{{ result.oppslag }}</div>
                  <div class='col-6 col-md-4 text-md-center text-left m-auto'>
                    <span class="hatsuon ja">発音:</span> <span
                      class='no'
                      v-if="result.uttale.length > 0"
                    >{{result.uttale[0].transkripsjon}}</span></div>
                  <div class='col-6 col-md-4 text-md-center text-right hinshi m-auto ja'>{{ partsOfSpeech[result.boy_tabell]}}</div>
                </div>
              </div>
            </div>
            <div class='imi my-3 ja'>
              <div
                v-for="def in result.definisjoner"
                :key="def.def_id + def.definisjon"
              >
                <span class='imi-count ml-4 mr-2'>{{def.prioritet}}</span>
                <span class='imi'>{{ def.definisjon }}</span><br>
              </div>

            </div>
          </div>
        </div>
        <div class="col-lg-2 col-xl-3"></div>
      </div>
    </div>
  </div>

</template>

<script>
import api from '../api.js'

export default {
  name: "Search",
  data () {
    return {
      suggestions: [],
      results: [],
      q: '',
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
      }
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
  },
  computed: {
    filteredResults() {
      let filtered = []
      if (this.q.length > 1) {
        filtered = this.results
        .filter(item => {
          if (item.oppslag.toLowerCase().includes(this.q)) {
            return true
          }
        })
        .sort((a,b) => {
          if (a.oppslag.toLowerCase().indexOf(this.q) > b.oppslag.toLowerCase().indexOf(this.q)) {
            return 1
          } else if (a.oppslag.toLowerCase().indexOf(this.q) < b.oppslag.toLowerCase().indexOf(this.q)){
            return -1
          } else {
            if (a.oppslag > b.oppslag) {
              return 1
            } else {
              return -1
            }
          }

        })
      }
      return filtered
    }
  },
  mounted () {
    //this.getSuggestionList()
    this.getSearchResults()
console.log(this.$route.params)
  },
  watch: {
    q: function(val) {
      this.q = val.toLowerCase()
    },
    filteredResults(filteredArray) {
      if (this.q.length > 1 && filteredArray.length === 0) {
        this.getSuggestionList()
      }
    }
  }
}


</script>