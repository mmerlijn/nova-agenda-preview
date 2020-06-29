Nova.booting((Vue, router, store) => {
  Vue.component('nova-agenda-preview', require('./components/Tool')),
  Vue.component('preview', require('./components/Preview')),
  Vue.component('time-bar', require('./components/Timebar')),
  Vue.component('activity-bar', require('./components/ActivityBar')),
  Vue.component('appointments-bar', require('./components/AppointmentsBar'))
})
