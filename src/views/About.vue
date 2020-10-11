<template>
  <div class="container-fluid about-box">
    <div class='row'>
      <div
        class='col mx-auto'
        style="font-size: 14px"
      >
        <p style="font-weight: bold">
          {{ $t('about.p1.title')}}
        </p>
        <p>
          {{ $t('about.p1.text')}}
        </p>
        <p style="font-weight: bold">
          {{ $t('about.p2.title')}}
        </p>
        <p v-html="$t('about.p2.text')">
        </p>
        <p style="font-weight: bold">
          {{ $t('about.p3.title')}}
        </p>
        <p>
          {{ $t('about.p3.text')}}
        </p>
        <p style="font-weight: bold">
          {{ $t('about.p4.title')}}
        </p>
        <p>
          {{ $t('about.p4.text')}}
        </p>
        <p v-html="$t('about.p4.list')">
        </p>
        <p>
          {{ $t('about.p4.contact')}}
        </p>
      </div>
    </div>
  </div>
</template>
