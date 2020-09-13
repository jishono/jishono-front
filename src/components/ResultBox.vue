<template>
  <div class='card my-2'>
    <div class='card-header py-1'>
      <div class='row'>
        <div class=' col-md-4 text-center kotoba my-auto no'>{{ word.oppslag }}</div>
        <div class='col-6 col-md-4 text-md-center text-left m-auto'>
          <span class="hatsuon ja">発音:</span> <span
            class='no'
            v-if="word.uttale.length > 0"
          >{{word.uttale[0].transkripsjon}}</span>
          <span
            class="hatsuon ja"
            v-else
          >未登録</span>
        </div>
        <div class='col-6 col-md-4 text-md-center text-right hinshi m-auto ja'>
          <span @click="$emit('get-conjugations')">{{ partsOfSpeech[word.boy_tabell]}}
            <span v-if="word.boy_tabell === 'subst'">{{ getGender(word.pos)}}
            </span></span>
        </div>
      </div>
    </div>
    <div class='my-3 mx-3 ja'>
      <div
        class='row no-gutters'
        v-for="def in word.definisjoner"
        :key="def.def_id + def.definisjon"
      >
        <div
          class='col-fluid'
          style='width: 25px'
        >
          <span class='imi-count '>{{def.prioritet}}.</span>
        </div>
        <div class='col'>
          <span
            v-html="addFurigana(def.definisjon)"
            class='imi'
          ></span><br>
        </div>
      </div>
    </div>
    <div
      class='mx-3'
      v-if="word.relatert.length > 0"
    >
      <span class='ja-kanren'>関連語：</span>
      <span
        v-for="(relatedWord, i) in word.relatert"
        :key="word.lemma_id + relatedWord"
      >
        <span
          style="cursor: pointer; text-decoration: underline;"
          @click="$emit('kanren-click', relatedWord)"
        >{{relatedWord}}</span>
        <span v-if="i != word.relatert.length-1">,&nbsp;</span>
      </span>

    </div>
    <div class='d-flex justify-content-between mx-3 mb-2'>
      <div
        v-if="word.example_sentences && totalPages > 1 && showingExamples"
        style="display: flex; align-items: center; "
      >
        <i
          class="fa fa-chevron-left fa-sm"
          @click="previousPage"
        ></i>
        <span class="mx-1">{{page + 1}} / {{totalPages}}</span>
        <i
          class="fa fa-chevron-right"
          @click="nextPage"
        ></i>
      </div>
      <div v-else></div>
      <div
        class="text-right"
        style="display: flex; align-items: center; width=100%;"
      >
        <button
          class="btn shadow-none btn-sm mb-2"
          :class="showingExamples ? 'btn-primary' : 'btn-outline-primary'"
          type="button"
          data-toggle="collapse"
          @click="getExampleSentences(); showingExamples = !showingExamples"
        >
          <!--           <span
            v-if="loading"
            class="spinner-border spinner-border-sm"
          ></span> -->
          <span v-if="!loading">
            例文
          </span>
        </button>
        <button
          class="btn btn-sm shadow-none mb-2 ml-1 "
          :class="showingConjugations ? 'btn-primary' : 'btn-outline-primary'"
          type="button"
          data-toggle="collapse-conjugations"
          @click="getConjugations(); showingConjugations = !showingConjugations"
        >
          <span>
            活用
          </span>
        </button>
      </div>
    </div>
    <div
      class='collapse text-left ml-2 mb-2'
      :id="'collapse' + word.lemma_id"
    >
      <div
        v-for="(example, index) in exampleSentencesByPage"
        :key="word.lemma_id + 'i' + index"
        class="row no-gutters mb-1"
      >
        <div
          class='col-fluid text-right mr-2'
          style='width: 25px'
        >
          <span>{{index + 1 + (10*page)}}.</span>

        </div>
        <div class="col">
          <div class="no-example"> {{ example.no_setning}}</div>
          <div class="ja-example"> {{ example.ja_setning}}</div>
        </div>
      </div>

      <div
        v-if="word.example_sentences && word.example_sentences.length === 0"
        class="ja"
      >例文の登録がありません。</div>
    </div>
    <div
      class='collapse text-left mb-2'
      :id="'collapse-conjugations' + word.lemma_id"
    >
      <ConjugationBox
        v-bind:show="showingConjugations"
        v-bind:wordID="word.lemma_id"
        v-bind:pos="word.boy_tabell"
        v-bind:word="word.oppslag"
      >
      </ConjugationBox>
    </div>

  </div>
</template>

<script>
//import api from '../api.js'
import ConjugationBox from './ConjugationBox'

export default {
  name: 'ResultBox',
  components: {
    ConjugationBox
  },
  data () {
    return {
      loading: false,
      page: 0,
      showingExamples: false,
      showingConjugations: false,
      relatedWords: [],
      partsOfSpeech: {
        adj: "形容詞",
        adv: "副詞",
        det: "限定詞",
        interjeksjon: "感動詞",
        konjunksjon: "接続詞",
        preposisjon: "前置詞",
        prefiks: "接頭辞",
        pron: "代名詞",
        subjunksjon: "関係詞",
        subst: "名詞",
        verb: "動詞",
      },
    }
  },
  props: {
    word: Object,
    index: Number
  },
  computed: {
    totalPages () {
      if (this.word.example_sentences) {
        return Math.ceil(this.word.example_sentences.length / 10)
      } else {
        return 0
      }
    },
    exampleSentencesByPage () {
      if (this.word.example_sentences) {
        const start = this.page * 10
        const end = start + 10
        return this.word.example_sentences.slice(start, end)
      } else {
        return null
      }
    },
  },
  methods: {
    nextPage () {
      if (this.page < this.totalPages - 1) {
        this.page++
      }
    },
    previousPage () {
      if (this.page > 0) {
        this.page--
      }
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
    getExampleSentences () {
      const str = '#collapse' + this.word.lemma_id
      window.$(str).collapse('toggle')
      this.$emit('get-example-sentences');
    },
    getConjugations () {
      const str = '#collapse-conjugations' + this.word.lemma_id
      window.$(str).collapse('toggle')
    },
    addRuby (str) {
      const kanji = str.match(/.*?(?=\[)/)
      let furigana = str.match(/(?=\[).*(\])/)
      furigana = furigana[0].replace(/\[|\]/g, "")
      return `<ruby>${kanji}<rp>(</rp><rt>${furigana}</rt><rp>)</rp></ruby>`
    },
    addFurigana (str) {
      if (!str.match(/\[/)) {
        return str
      }
      const regex = new RegExp(/[^）、の]*?\]/g)
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
}
    </script>