<template>
  <div
    class="conjugation-box"
    v-if="conjugations.length > 0 || !hasConjugation.includes(pos)"
  >
    <div
      v-for="
    (part,
    i)
    in
    twoAndTwo"
      :key="i"
      class="my-2"
    >
      <!-- Adverbs -->
      <table
        v-if="pos === 'adv' && conjugations[0].boy_skjema === '17'"
        class='table table-sm table-bordered text-center'
      >
        <tr
          v-for="(conjugation,i) in adverbs"
          :key="conjugation.pattern"
        >
          <th>{{conjugation.text}}</th>
          <template v-for="pattern in part">
            <th
              v-if="i == 0"
              :key="pattern.lemma_id + pattern.paradigme"
            >{{ pattern[conjugation.pattern]}}
            </th>
            <td
              v-else
              :key="pattern.lemma_id + pattern.paradigme"
            > {{ pattern[conjugation.pattern]}}
            </td>
          </template>
        </tr>
      </table>
      <table
        v-if="pos === 'adv' && conjugations[0].boy_skjema === '27'"
        class='table table-sm table-bordered text-center'
      >
        <tr>
          <th></th>
          <th>{{ conjugations[0].pos}}</th>
        </tr>
        <tr>
          <th>普通形</th>
          <td>{{ word }}</td>
        </tr>
      </table>
      <!-- Adjectives -->
      <table
        v-if="pos === 'adj'"
        class='table table-sm table-bordered text-right'
      >
        <tr>
          <th></th>
          <th></th>
          <th
            v-for="pattern in part"
            :key="pattern.paradigme + 'pos'"
            class="text-center"
          >{{ pattern.pos}}</th>
        </tr>
        <tr style='border-top-style: solid; border-top-width: thin; '>
          <th
            rowspan='3'
            class='rotate-container th-wrap'
          >
            <div>単 数 形</div>
          </th>
          <th style="width: 30%;">男・女性形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'm_en'"
          >{{ pattern.m_entall}}</td>
        </tr>
        <tr>
          <th>中性形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'n_en'"
          >{{ pattern.n_entall}}</td>
        </tr>
        <tr>
          <th>既知形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'be_en'"
          >{{ pattern.bestemt_entall}}</td>
        </tr>
        <tr style='border-top-style: solid; border-top-width: thin;'>
          <th rowspan='1'></th>
          <th>複数形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'f'"
          >{{ pattern.flertall}}</td>
        </tr>
        <tr style='border-top-style: solid; border-top-width: thin;'>
          <th rowspan='3'></th>
          <th>比較級</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'k'"
          >{{ pattern.komparativ}}</td>
        </tr>
        <tr>
          <th>最上級</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 's'"
          >{{ pattern.superlativ}}</td>
        </tr>
        <tr>
          <th>最上級既知形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'sb'"
          >{{ pattern.superlativ_bestemt}}</td>
        </tr>
      </table>
      <!-- Determinatives -->
      <table
        v-if="pos === 'det' && ['14','19'].includes(conjugations[0].boy_skjema)"
        class='table table-sm table-bordered text-center'
      >
        <tr
          v-for="(conjugation,i) in determinatives"
          :key="conjugation.pattern"
        >
          <th>{{conjugation.text}}</th>
          <template v-for="pattern in part">
            <th
              v-if="i == 0"
              :key="pattern.lemma_id + pattern.paradigme"
            >{{ pattern[conjugation.pattern]}}
            </th>
            <td
              v-else
              :key="pattern.lemma_id + pattern.paradigme"
            >{{ pattern[conjugation.pattern]}}
            </td>
          </template>
        </tr>
      </table>

      <table
        v-if="pos === 'det' && ['28','49'].includes(conjugations[0].boy_skjema)"
        class='table table-sm table-bordered text-center'
      >
        <tr>
          <th></th>
          <th>{{ conjugations[0].pos}}</th>
        </tr>
        <tr>
          <th>普通形</th>
          <td>{{ word }}</td>
        </tr>
      </table>

      <!-- Pronouns -->
      <table
        v-if="pos === 'pron' && conjugations[0].boy_skjema === '34'"
        class='table table-sm table-bordered text-center'
      >
        <tr>
          <th></th>
          <th>{{ conjugations[0].pos}}</th>
        </tr>
        <tr>
          <th>普通形</th>
          <td>{{ word }}</td>
        </tr>
      </table>

      <table
        v-if="pos === 'pron' && conjugations[0].boy_skjema === '22'"
        class='table table-sm table-bordered text-center'
      >
        <tr
          v-for="(conjugation,i) in pronouns"
          :key="conjugation.pattern"
        >
          <th>{{conjugation.text}}</th>
          <template v-for="pattern in part">
            <th
              v-if="i == 0"
              :key="pattern.lemma_id + pattern.paradigme"
            >{{ pattern[conjugation.pattern]}}
            </th>
            <td
              v-else
              :key="pattern.lemma_id + pattern.paradigme"
            >{{ pattern[conjugation.pattern]}}
            </td>
          </template>
        </tr>
      </table>
      <table
        v-if="pos === 'subst' && conjugations[0].boy_skjema !== '36'"
        class='table table-sm table-bordered text-center'
      >
        <tr
          v-for="(conjugation,i) in nouns"
          :key="conjugation.pattern"
        >
          <th>{{conjugation.text}}</th>
          <template
            v-for="pattern in part"
            v-bind:iteration=2
          >
            <th
              v-if="i == 0"
              :key="pattern.lemma_id + pattern.paradigme"
            >{{ pattern[conjugation.pattern]}}
            </th>
            <td
              v-else
              :key="pattern.lemma_id + pattern.paradigme"
            ><span v-if="conjugation.pattern === 'ubestemt_entall'">
                {{getPrefix(pattern.pos) }}
              </span>
              {{ pattern[conjugation.pattern]}}
            </td>
          </template>
        </tr>
      </table>

      <table
        v-if="pos === 'subst' && conjugations[0].boy_skjema === '36'"
        class='table table-sm table-bordered text-center'
      >
        <tr>
          <th></th>
          <th>活用不可</th>
        </tr>
        <tr>
          <th>普通形</th>
          <td>{{ word }}</td>
        </tr>
      </table>

      <table
        v-if="pos === 'verb'"
        class='table table-sm table-bordered text-right'
      >
        <tr>
          <th></th>
          <th></th>
          <th
            v-for="pattern in part"
            :key="pattern.paradigme + 'pos'"
            class="text-center"
          >{{ pattern.pos}}</th>
        </tr>
        <tr>
          <th rowspan='6'></th>
          <th style="width: 30%;">不定形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'infinitiv'"
          >å {{ pattern.infinitiv}}</td>
        </tr>
        <tr>
          <th>現在形 </th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'presens'"
          >{{ pattern.presens}}</td>
        </tr>
        <tr>
          <th>過去形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'preteritum'"
          >{{ pattern.preteritum}}</td>
        </tr>
        <tr>
          <th>現在完了形 </th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'presens_perfektum'"
          >har {{ pattern.presens_perfektum}}</td>
        </tr>
        <tr>
          <th>現在分詞形 </th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'presens_partisipp'"
          >{{ pattern.presens_partisipp}}</td>
        </tr>
        <tr>
          <th>命令形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'imperativ'"
          >{{ pattern.imperativ}}</td>
        </tr>
        <tr style='border-top-style: solid; border-top-width: thin;'>
          <th
            rowspan='4'
            class='rotate-container th-wrap'
          >
            <div>完了分詞形</div>
          </th>
          <th>男・女性形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'perf_part_mf'"
          >{{ pattern.perf_part_mf}}</td>
        </tr>
        <tr>
          <th>中性形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'perf_part_n'"
          >{{ pattern.perf_part_n}}</td>
        </tr>
        <tr>
          <th>既知形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'perf_part_bestemt'"
          >{{ pattern.perf_part_bestemt}}</td>
        </tr>
        <tr>
          <th>複数形</th>
          <td
            v-for="pattern in part"
            :key="pattern.paradigme + 'perf_part_flertall'"
          >{{ pattern.perf_part_flertall}}</td>
        </tr>
      </table>
    </div>
    <table
      v-if="!hasConjugation.includes(pos)"
      class='table table-sm table-bordered text-right'
    >
      <tr>
        <th></th>
        <th class="text-center">{{ pos}}</th>
      </tr>
      <tr>
        <th>普通形</th>
        <td>{{ word }}</td>
      </tr>
    </table>

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
      adverbs: [
        { text: '', pattern: 'pos' },
        { text: '普通形	', pattern: 'positiv' },
        { text: '比較級	', pattern: 'komparativ' },
        { text: '未知複数形	', pattern: 'superlativ' },
      ],
      determinatives: [
        { text: '', pattern: 'pos' },
        { text: '男性単数形', pattern: 'm_entall', prefix: true },
        { text: '女性単数形', pattern: 'f_entall' },
        { text: '中性単数形', pattern: 'n_entall' },
        { text: '複数形', pattern: 'flertall' },
        { text: '既知形', pattern: 'bestemt_entall' }
      ],
      pronouns: [
        { text: '', pattern: 'pos' },
        { text: '主格', pattern: 'subjektsform' },
        { text: '目的格', pattern: 'objektsform' },
      ],
      nouns: [
        { text: '', pattern: 'pos' },
        { text: '未知単数形	', pattern: 'ubestemt_entall' },
        { text: '既知単数形	', pattern: 'bestemt_entall' },
        { text: '未知複数形	', pattern: 'ubestemt_flertall' },
        { text: '既知複数形	', pattern: 'bestemt_flertall' },
      ],
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
    twoAndTwo () {
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
  /*   mounted () {
      if (this.wordID && this.pos && this.hasConjugation.includes(this.pos)) {
        this.getConjugations(this.wordID, this.pos)
      }
    }, */
};
    </script>