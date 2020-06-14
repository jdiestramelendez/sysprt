
<template>
<div class="container">
  <div class="row">
    <div v-bind:class="selectEvento == 0 ? 'col-lg-4' : 'form-control-lg'">
      <select class="form-control " v-model="selectEvento"  >
        <option value="0" selected>Novo</option>
        <option v-for="evento in eventos" :value="evento.id" >{{evento.nome}}</option>
      </select>
    </div>
    <div class="col-lg-6" v-if="selectEvento == 0" >
      <input class="form-control" type="text" v-model="nomeEvento" placeholder="Nome do evento"/>
      </div>
      <div class="col-lg-2" v-if="selectEvento == 0" >
      <button class="btn  btn-outline-primary "> Salvar </button>
      <a href="/eventos" class="btn btn-outline-danger ml-1"> Cancelar </a>
      </div>
  </div>
<div class="container">
  <eventos-edit></eventos-edit>
</div>

</div>
</template>

<script>
export default {
  data: () => {
    return {
      eventos: [],
      selectEvento: null,
      nomeEvento: null
    };
  },
  mounted() {
    this.getEventos();
  },
  watch: {
       selectEvento: function mudancaevento() { 
          this.$eventBus.$emit("eventoescolhido",self.selectEvento)
        }          
  },
  methods: {
    getEventos() {
      self = this;

      axios
        .post("/geteventos")
        .then(response => {
          console.log(response);
          self.eventos = response.data;
        })
        .catch(error => {
          console.error("error: ", error);
        })
        .then(() => {});
    },
  }
};
</script>