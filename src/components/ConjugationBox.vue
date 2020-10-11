<template>
  <div
    class="conjugation-box"
    v-if="conjugations.length > 0 || !hasConjugation.includes(pos)"
  >
    <table
      v-if="!hasConjugation.includes(pos) || 
      (pos === 'adv' && conjugations[0].boy_skjema === '27') ||
      (pos === 'det' && ['28','49'].includes(conjugations[0].boy_skjema)) ||
      (pos === 'pron' && conjugations[0].boy_skjema === '34') ||
      (pos === 'subst' && conjugations[0].boy_skjema === '36')"
      class='table table-sm table-bordered text-right'
    >
      <tr>
        <th></th>
        <th class="text-center">{{ pos }}</th>
      </tr>
      <tr>
        <th>{{$t('words.conjugations.normal')}}</th>
        <td>{{ word }}</td>
      </tr>
    </table>
    <div
      v-else
      v-for="(divide,i1) in dividedResults"
      :key="i1"
      class="my-2"
    >
      <table class='table table-sm table-bordered text-right'>
        <tr
          v-for="(conjugation,i2) in patterns[pos]"
          :key="conjugation.pattern"
          :class="conjugation.borderTop ? 'border-top' : ''"
        >
          <th
            v-if="conjugation.rowspan"
            :rowspan=conjugation.rowspan
            :key="conjugation.pattern + conjugation.rowspan"
            class='rotate-container th-wrap pl-2'
          >
            <div v-if="conjugation.rowText">{{$t('words.conjugations.' + pos + '.' + conjugation.rowText )}}</div>
          </th>
          <th v-if="conjugation.pattern != 'pos'" style="width: 30%;">{{$t('words.conjugations.' + pos + '.' + conjugation.pattern )}}</th>
          <th v-else style="width: 30%;"></th>
          <template v-for="pattern in divide">
            <th
              v-if="i2 == 0"
              :key="pattern.lemma_id + pattern.paradigme"
              class="text-center"
            >
              {{ pattern[conjugation.pattern]}}
            </th>
            <td
              v-else
              :key="pattern.lemma_id + pattern.paradigme"
            > <span v-if="conjugation.prefix">{{ conjugation.prefix }}</span>
              <span v-if="conjugation.genderPrefix">{{ getPrefix(pattern.pos) }}</span>
              {{ pattern[conjugation.pattern]}}
            </td>
          </template>
        </tr>
      </table>
    </div>

  </div>
</template>

<script>
import api from '../api.js'

export default {
  name: 'ConjugationBox',
  data () {
    return {
      conjugations: [],
      hasConjugation: ['adj', 'adv', 'det', 'pron', 'subst', 'verb'],
      patterns: {
        adj: [
          { text: '', pattern: 'pos', rowspan: 1 },
          { text: '男・女性形', pattern: 'm_entall', rowspan: 3, rowText:'entall' },
          { text: '中性形', pattern: 'n_entall' },
          { text: '既知形', pattern: 'bestemt_entall' },
          { text: '複数形', pattern: 'flertall', rowspan: 1, borderTop: true },
          { text: '比較級', pattern: 'komparativ', rowspan: 3, borderTop: true },
          { text: '最上級', pattern: 'superlativ' },
          { text: '最上級既知形	', pattern: 'superlativ_bestemt' },
        ],
        adv: [
          { text: '', pattern: 'pos' },
          { text: '普通形', pattern: 'positiv' },
          { text: '比較級', pattern: 'komparativ' },
          { text: '未知複数形', pattern: 'superlativ' },
        ],
        det: [
          { text: '', pattern: 'pos', rowspan: 1 },
          { text: '男性単数形', pattern: 'm_entall', rowspan: 3, rowText: 'entall' },
          { text: '女性単数形', pattern: 'f_entall' },
          { text: '中性単数形', pattern: 'n_entall' },
          { text: '複数形', pattern: 'flertall', rowspan: 2, borderTop: true },
          { text: '既知形', pattern: 'bestemt_entall' }
        ],
        pron: [
          { text: '', pattern: 'pos' },
          { text: '主格', pattern: 'subjektsform' },
          { text: '目的格', pattern: 'objektsform' },
        ],
        subst: [
          { text: '', pattern: 'pos' },
          { text: '未知単数形', pattern: 'ubestemt_entall', genderPrefix: true },
          { text: '既知単数形', pattern: 'bestemt_entall' },
          { text: '未知複数形', pattern: 'ubestemt_flertall' },
          { text: '既知複数形', pattern: 'bestemt_flertall' },
        ],
        verb: [
          { text: '', pattern: 'pos', rowspan: 6 },
          { text: '不定形', pattern: 'infinitiv', prefix: 'å ' },
          { text: '現在形', pattern: 'presens' },
          { text: '過去形', pattern: 'preteritum' },
          { text: '現在完了形', pattern: 'presens_perfektum', prefix: 'har ' },
          { text: '命令形', pattern: 'imperativ' },
          { text: '男・女性形', pattern: 'perf_part_mf', rowspan: 4, rowText: 'perfektum', borderTop: true },
          { text: '中性形', pattern: 'perf_part_n' },
          { text: '既知形', pattern: 'perf_part_bestemt' },
          { text: '複数形', pattern: 'perf_part_flertall' },
          { text: '現在分詞形', pattern: 'presens_partisipp', rowspan: 1, borderTop: true },
        ]
      },
    }
  },
  props: {
    show: Boolean,
    wordID: Number,
    pos: String,
    word: String,

  },
  methods: {
    close () {
      this.$emit("close")
      this.conjugations = []
    },
    getPrefix (paradigm) {
      if (paradigm[0] === 'm') return 'en '
      if (paradigm[0] === 'f') return 'ei '
      if (paradigm[0] === 'n') return 'et '
    },
    getConjugations (wordID, pos) {
      if (this.hasConjugation.includes(pos)) {
        api.post('/conjugations/' + wordID, { pos: pos })
          .then(result => {
            this.conjugations = result.data
          })
      }
    }
  },
  computed: {
    dividedResults () {
      let divider
      if (window.innerWidth < 600) {
        divider = 2
      } else {
        divider = 3
      }
      const k = this.conjugations.length
      let splitArrays = []
      for (let i = 0; i < k; i = i + divider) {
        splitArrays.push(this.conjugations.slice(i, i + divider))
      }
      return splitArrays
    }
  },
  watch: {
    show: function (val) {
      if (val) {
        this.getConjugations(this.wordID, this.pos)
      }
    }
  },
};
    </script>