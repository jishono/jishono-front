<template>
  <transition
    name="fade"
    appear
  >
    <div
      class="modal-overlay"
      v-if="show"
      @click.self="close"
    >
      <div
        class="conjugation-modal"
        v-if="conjugations.length > 0"
      >
        <table
          v-if="pos === 'subst'"
          class='table table-sm table-bordered text-center'
        >
          <tr>
            <th></th>
            <th
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'pos'"
            >{{ pattern.pos}}</th>
          </tr>
          <tr>
            <th>未知単数形</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'ub_en'"
            >{{ pattern.ubestemt_entall}}</td>
          </tr>
          <tr>
            <th>既知単数形</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'b_en'"
            >{{ pattern.bestemt_entall}}</td>
          </tr>
          <tr>
            <th>未知複数形</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'ub_f'"
            >{{ pattern.ubestemt_flertall}}</td>
          </tr>
          <tr>
            <th>既知複数形</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'b_f'"
            >{{ pattern.bestemt_flertall}}</td>
          </tr>
        </table>
        <table
          v-if="pos === 'adj'"
          class='table table-sm table-bordered text-center'
        >
          <tr>
            <th></th>
            <th
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'pos'"
            >{{ pattern.pos}}</th>
          </tr>
          <tr>
            <th>男性・女性（単）</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'm_en'"
            >{{ pattern.m_entall}}</td>
          </tr>
          <tr>
            <th>中性（単）</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'n_en'"
            >{{ pattern.n_entall}}</td>
          </tr>
          <tr>
            <th>既知形（単）</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'be_en'"
            >{{ pattern.bestemt_entall}}</td>
          </tr>
          <tr>
            <th>複数形</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'f'"
            >{{ pattern.flertall}}</td>
          </tr>
          <tr>
            <th>比較級</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'k'"
            >{{ pattern.komparativ}}</td>
          </tr>
          <tr>
            <th>最上級</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 's'"
            >{{ pattern.superlativ}}</td>
          </tr>
          <tr>
            <th>最上級既知形</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'sb'"
            >{{ pattern.superlativ_bestemt}}</td>
          </tr>
        </table>
        <table
          v-if="pos === 'adv' && conjugations[0].boy_skjema === '17'"
          class='table table-sm table-bordered text-center'
        >
          <tr>
            <th></th>
            <th
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'pos'"
            >{{ pattern.pos}}</th>
          </tr>
          <tr>
            <th>普通形</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'p'"
            >{{ pattern.positiv}}</td>
          </tr>
          <tr>
            <th>比較級</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 'k'"
            >{{ pattern.komparativ}}</td>
          </tr>
          <tr>
            <th>最上級</th>
            <td
              v-for="pattern in conjugations"
              :key="pattern.paradigme + 's'"
            >{{ pattern.superlativ}}</td>
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
            <th>普通形形</th>
            <td>{{ word }}</td>
          </tr>
        </table>
        <table
          v-if="pos === 'det'"
          class='table table-sm table-bordered text-center'
        >
          <tr
            v-for="(conjugation,i) in determinatives"
            :key="conjugation.pattern"
          >
            <th>{{conjugation.text}}</th>
            <template v-for="pattern in conjugations">
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
          v-if="pos === 'verb'"
          class='table table-sm table-bordered text-center'
        >
          <tr
            v-for="(time,i) in verbs"
            :key="time.pattern"
          >
            <th>{{time.text}}</th>
            <template v-for="pattern in conjugations">
              <th
                v-if="i == 0"
                :key="pattern.lemma_id + pattern.paradigme"
              >{{ pattern[time.pattern]}}
              </th>
              <td
                v-else
                :key="pattern.lemma_id + pattern.paradigme"
              >{{ pattern[time.pattern]}}
              </td>
            </template>
          </tr>
        </table>
      </div>
    </div>
  </transition>
</template>

<script>
import api from '../api.js'

export default {
  name: 'ConjugationModal',
  data () {
    return {
      conjugations: [],
      determinatives: [
        { text: '', pattern: 'pos' },
        { text: '男性単数形', pattern: 'm_entall' },
        { text: '女性単数形', pattern: 'f_entall' },
        { text: '中性単数形', pattern: 'n_entall' },
        { text: '複数形', pattern: 'bestemt_entall' },
        { text: '既知形', pattern: 'flertall' }
      ],
      verbs: [
        { text: '', pattern: 'pos' },
        { text: '不定形', pattern: 'infinitiv' },
        { text: '現在形', pattern: 'presens' },
        { text: '過去形', pattern: 'preteritum' },
        { text: '現在完了形', pattern: 'presens_perfektum' },
        { text: '命令形', pattern: 'imperativ' },
        { text: '男性・女性形', pattern: 'perf_part_mf' },
        { text: '中性形	', pattern: 'perf_part_n' },
        { text: '既知形	', pattern: 'perf_part_bestemt' },
        { text: '複数形', pattern: 'perf_part_flertall' },
        { text: '現在分詞形', pattern: 'presens_partisipp' },
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
    getConjugations (wordID, pos) {
      api.post('/conjugations/' + wordID, { pos: pos })
        .then(result => {
          console.log(result.data)
          this.conjugations = result.data
        })
    }
  },
  watch: {
    wordID: function () {
      if (this.wordID && this.pos) {
        this.getConjugations(this.wordID, this.pos)
      }
    }
  },
};
    </script>