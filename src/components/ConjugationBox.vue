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
          v-for="(conjugation,i2) in wordClasses[pos]"
          :key="conjugation.form"
          :class="conjugation.borderTop ? 'border-top' : ''"
        >
          <th
            v-if="conjugation.rowspan"
            :rowspan=conjugation.rowspan
            :key="conjugation.form + conjugation.rowspan"
            class='rotate-container th-wrap pl-2'
          >
            <div v-if="conjugation.rowText">{{$t('words.conjugations.' + pos + '.' + conjugation.rowText )}}</div>
          </th>
          <th
            v-if="conjugation.form != 'pos'"
            style="width: 30%;"
          >{{$t('words.conjugations.' + pos + '.' + conjugation.form )}}</th>
          <th
            v-else
            style="width: 30%;"
          ></th>
          <template v-for="pattern in divide">
            <th
              v-if="i2 == 0"
              :key="pattern.lemma_id + pattern.paradigme"
              class="text-center"
            >
              {{ pattern[conjugation.form]}}
            </th>
            <td
              v-else
              :key="pattern.lemma_id + pattern.paradigme"
            > <span v-if="conjugation.prefix">{{ conjugation.prefix }}</span>
              <span v-if="conjugation.genderPrefix">{{ getPrefix(pattern.pos) }}</span>
              {{ pattern[conjugation.form]}}
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
      wordClasses: {
        adj: [
          { form: 'pos', rowspan: 1 },
          { form: 'm_entall', rowspan: 3, rowText: 'entall' },
          { form: 'n_entall' },
          { form: 'bestemt_entall' },
          { form: 'flertall', rowspan: 1, borderTop: true },
          { form: 'komparativ', rowspan: 3, borderTop: true },
          { form: 'superlativ' },
          { form: 'superlativ_bestemt' },
        ],
        adv: [
          { form: 'pos' },
          { form: 'positiv' },
          { form: 'komparativ' },
          { form: 'superlativ' },
        ],
        det: [
          { form: 'pos', rowspan: 1 },
          { form: 'm_entall', rowspan: 3, rowText: 'entall' },
          { form: 'f_entall' },
          { form: 'n_entall' },
          { form: 'flertall', rowspan: 2, borderTop: true },
          { form: 'bestemt_entall' }
        ],
        pron: [
          { form: 'pos' },
          { form: 'subjektsform' },
          { form: 'objektsform' },
        ],
        subst: [
          { form: 'pos' },
          { form: 'ubestemt_entall', genderPrefix: true },
          { form: 'bestemt_entall' },
          { form: 'ubestemt_flertall' },
          { form: 'bestemt_flertall' },
        ],
        verb: [
          { form: 'pos', rowspan: 6 },
          { form: 'infinitiv', prefix: 'å ' },
          { form: 'presens' },
          { form: 'preteritum' },
          { form: 'presens_perfektum', prefix: 'har ' },
          { form: 'imperativ' },
          { form: 'perf_part_mf', rowspan: 4, rowText: 'perfektum', borderTop: true },
          { form: 'perf_part_n' },
          { form: 'perf_part_bestemt' },
          { form: 'perf_part_flertall' },
          { form: 'presens_partisipp', rowspan: 1, borderTop: true },
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