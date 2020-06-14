<template>
  <div class="row fundo-color" style="height: 65vh;">
    <img src="img/carregar.gif" v-if="parametroscarregados == false && evento != null" class="col-lg-2 offset-5" />

      <div class="col-lg-3 bg-light border border-dark" v-if="parametroscarregados == true" >
         <h3 class="display text-center">Selecione o Parâmetro</h3>
        <div class="container-lg" style="overflow: auto;">         
          <div v-for="parametro in parametros" class="align-self-center" >
            <a href="#" class="btn btn-outline-dark container" @click.prevent="addParametro(parametro)" >{{parametro.descricao + " - " + parametro.texto}}</a>
          </div>
        </div>
      </div>

      <div class="col-lg border border-primary" v-if="parametroscarregados == true">
          <h3 class="text-dark">Defina parâmetros e valores para o evento</h3>
          <div class="form-group">   
               <div class="row border-bottom" v-for="(selectParametro, index) in parametrosSelecionados">
                 <div class="col h3">
                 <input v-model="selectParametro.descricao" readonly="false" class="h3 form-control" style="margin-top: 8px !important;" />
                 </div>
                 <div class="col">
                  <input type="number" v-model="selectParametro.value" class=" h3 form-control" :placeholder="selectParametro.texto" />
                 </div>
                 <div class="col">
                   <input type="number" v-if="selectParametro.id == 3" v-model="selectParametro.latitude" class=" h3 form-control" placeholder="Latitude" />
                 </div>
                 <div class="col">
                       <input type="number" v-if="selectParametro.id == 3" v-model="selectParametro.longitude" class=" h3 form-control" placeholder="Longitude" />
                 </div>
                 <div class="col text-right">
                  <a v-if="selectParametro.id == 3" href="#" class="btn btn-outline-light" @click.prevent="removeParametro(index)"  >                     
                  <i class="fas fa-list text-primary"></i>
                   </a>
                  <a v-if="selectParametro.id == 3" href="#" class="btn btn-outline-light" @click.prevent="removeParametro(index)"  >
                            <i class="fas fa-map-marked-alt text-success"></i>
                   </a>
                   <a href="#" class="btn btn-outline-light" @click.prevent="removeParametro(index)"  >
                            <i class="fas fa-times text-danger h3"></i>
                   </a>
                        </div>
                 </div>
          </div>
      </div>

      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: () => {
    return {
      evento: null,
      parametroscarregados: false,
      parametros: [],
      parametrosSelecionados: [],
      evento: null
    };
  },
  mounted() {
    this.$eventBus.$on(["eventoescolhido"], data => {
      this.evento = data;
      this.carregarParametros();
      console.log(data);
    });
  },
  methods: {
    carregarParametros() {
      self = this;
      axios
        .get("/eventos/parametros")
        .then(response => {
          console.log(response);
          self.parametros = response.data;
          self.parametroscarregados = true;
        })
        .catch(error => {
          console.error("error: ", error);
        })
        .then(() => {});
    },
    addParametro($parametro){
      this.parametrosSelecionados.push($parametro)
    }, removeParametro($parametro){
    this.parametrosSelecionados.splice($parametro,$parametro)
    }
  }
};
</script>