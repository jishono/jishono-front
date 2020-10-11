<template>
  <transition
    name="fade"
    appear
  >
    <div
      class="modal-overlay"
      v-if="show"
      @click.self="$emit('close-feedback-dialog')"
    >
      <div class="vipps-modal">
        <div class="text-right mb-2">
          <span
            class="float-left"
            style="font-weight: bold"
          > {{ $t('feedback.title') }}</span>
          <button
            class="btn bg-transparent p-0"
            style="margin-top: -16px"
            @click="$emit('close-feedback-dialog')"
          >
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div> {{ $t('words.word') }} {{word.oppslag}}</div>
        <div
          v-for="def in this.word.definisjoner"
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
          :placeholder="$t('feedback.placeholder')"
        ></textarea>
        <div class="row no-gutters mt-2">
          <div class="col d-flex justify-content-between align-items-center">
            <span
              v-html="$t('feedback.baksida')"
              class="donate-text"
            ></span>
            <button
              class="btn btn-sm shadow-none btn-outline-primary ml-2"
              type="button"
              @click="sendFeedback"
              style="min-width: 50px"
            >
              <span>
                {{ $t('feedback.send') }}
              </span>
            </button>

          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import api from '../api.js'

export default {
  name: 'FeedbackDialog',
  data () {
    return {
      feedback: "",
    }
  },
  props: {
    word: Object,
    show: Boolean
  },
  methods: {
    sendFeedback () {
      const id = this.word.lemma_id
      api.post('/words/' + id + '/feedback', {
        feedback: this.feedback
      })
        .then(() => {
          this.$emit('close-feedback-dialog')
        })
    },
  },
}
</script>