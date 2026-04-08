
import App from './App.vue'
import MineBootstrap from './bootstrap'

const app = createApp(App)

MineBootstrap(app).then(() => {
  app.mount('#app')
}).catch((err) => {
  console.error('MineAdmin-UI start fail', err)
})
