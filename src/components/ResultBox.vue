<template>
  <div class='card my-2'>
    <div
      class='card-header py-1'
      :id="'result' + word.lemma_id"
    >
      <div class='row ja'>
        <div class=' col-md-4 text-center result-title my-auto'>{{ word.oppslag }}</div>
        <div class='col-6 col-md-4 text-md-center text-left m-auto'>
          <div v-if="word.uttale.length > 0">
            <span class="ja">{{ $t('interface.pronounciation') }}:</span>
            <span class="pronounctiation">
              {{word.uttale[0].transkripsjon}}
            </span>
          </div>
        </div>
        <div
          class='col-6 col-md-4 text-md-center text-right m-auto'
          v-if="word.boy_tabell"
        >
          <span v-if="partsOfSpeech.includes(word.boy_tabell)">{{ $t('words.pos.' + word.boy_tabell) }}</span><span v-if="word.boy_tabell === 'subst'">{{ $t('words.gender.' + getGender(word.pos)) }}
          </span>
        </div>
      </div>
    </div>
    <div class='my-2 mx-3 ja'>
      <div
        class='row no-gutters imi'
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
          <span v-html="addFurigana(def.definisjon)"></span><br>
        </div>
      </div>
    </div>
    <div
      class='mx-3 mb-1'
      v-if="word.definisjoner[0].wiki === 1"
    >
      <img src='@/assets/wikipedia_logo_20px.png'>
      <span
        class="ml-1"
        style=" font-size: 14px"
      >
        {{ $t('interface.wikipedia') }}</span>
    </div>
    <div
      class='mx-3'
      v-if="word.relatert.length > 0"
    >
      <span class="ja related">
        {{ $t('interface.related') }}
      </span>
      <span
        v-for="(relatedWord, i) in word.relatert"
        :key="word.lemma_id + relatedWord"
      >
        <span
          class="pointer underline"
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
          class="fa fa-chevron-left fa-sm pt-0"
          @click="previousPage"
        ></i>
        <span class="mx-1 pages">{{page + 1}} / {{totalPages}}</span>
        <i
          class="fa fa-chevron-right pt-0"
          @click="nextPage"
        ></i>
      </div>
      <div v-else></div>
      <div class="text-right button-row">
        <button
          class="btn shadow-none btn-sm mb-2"
          :class="showingExamples ? 'btn-primary' : 'btn-outline-primary'"
          type="button"
          data-toggle="collapse"
          @click="getExampleSentences"
        >
          <span v-if="!loading">
            {{ $t('interface.examples') }}
          </span>
        </button>
        <button
          class="btn btn-sm shadow-none mb-2 ml-1 "
          :class="showingConjugations ? 'btn-primary' : 'btn-outline-primary'"
          type="button"
          data-toggle="collapse-conjugations"
          @click="getConjugations"
        >
          <span>
            {{ $t('interface.conjugation') }}
          </span>
        </button>
        <button
          class="btn btn-sm btn-outline-primary shadow-none mb-2 ml-1"
          type="button"
          @click="$emit('feedback-click')"
        >
          <i
            class="fa fa-comment-o"
            style="font-size: 18px"
          ></i>
        </button>
      </div>
    </div>
    <div
      class='collapse text-left ml-2 mr-3 mb-2'
      :id="'collapse' + word.lemma_id"
    >
      <div
        v-for="(example, index) in exampleSentencesByPage"
        :key="word.lemma_id + 'i' + index"
        class="row no-gutters mb-1"
      >
        <div
          class='col-fluid text-right mr-2'
          style='width: 32px'
        >
          <span>{{index + 1 + (10*page)}}.</span>

        </div>
        <div class="col ja example">
          <div> {{ example.no_setning}}</div>
          <div class="ja"> {{ example.ja_setning}}</div>
        </div>
      </div>

      <div
        v-if="word.example_sentences && word.example_sentences.length === 0"
        class="ja ml-2"
      >{{ $t('interface.no_examples') }}</div>
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
      partsOfSpeech: [
        'adj',
        'adv',
        'det',
        'interjeksjon',
        'konjunksjon',
        'preposisjon',
        'prefiks',
        'pron',
        'subjunksjon',
        'subst',
        'verb'
      ],
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
    scrollToCardHeader () {
      let cardHeader = document.getElementById('result' + this.word.lemma_id)
      cardHeader.scrollIntoView({ behavior: 'smooth' })
    },
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
          return 'f'
        }
      }
      for (pos of posArray) {
        if (pos.includes('n')) {
          return 'n'
        }
        if (pos.includes('m')) {
          return 'm'
        }
      }
      return 'subst_uboy'
    },
    getExampleSentences () {
      this.showingExamples = !this.showingExamples
      this.scrollToCardHeader()
      const str = '#collapse' + this.word.lemma_id
      window.$(str).collapse('toggle')
      this.$emit('get-example-sentences');
    },
    getConjugations () {
      this.showingConjugations = !this.showingConjugations
      this.scrollToCardHeader()
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