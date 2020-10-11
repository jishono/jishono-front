<template>
  <transition
    name="fade"
    appear
  >
    <div
      class="modal-overlay"
      v-if="show"
      @click.self="$emit('close-vipps-dialog')"
    >
      <div class="vipps-modal">
        <div class="text-right mb-2">
          <span
            class="float-left"
            style="font-weight: bold"
          >{{ $t('vipps_dialog.headline') }}</span>
          <button
            class="btn bg-transparent p-0"
            style="margin-top: -16px"
            @click="$emit('close-vipps-dialog')"
          >
            <i class="fa fa-times"></i>
          </button>
        </div>
        <p>{{ $t('vipps_dialog.p1') }}</p>
        <p>{{ $t('vipps_dialog.p2') }}</p>
        <img
          class="img-fluid text-center mt-4"
          src='@/assets/vipps.png'
        >
      </div>
    </div>
  </transition>
</template>

<script>

export default {
  name: 'VippsDialog',
  data () {
    return {
    }
  },
  props: {
    show: Boolean
  },
}
</script>