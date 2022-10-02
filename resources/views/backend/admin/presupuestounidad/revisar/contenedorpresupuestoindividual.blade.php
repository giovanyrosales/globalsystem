<link href="{{ asset('css/cssacordeon.css') }}" type="text/css" rel="stylesheet" />


<section class="col-12" id="bloquecontenedor" style="display: none">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <form class="form-vertical">
                        <div style="margin-left: 20px">
                            <label style="color: darkgreen; font-size: 20px; font-family: arial">Total ${{$totalvalor}}</label>
                        </div>

                        <div class="col-12">
                            <!-- Custom Tabs -->
                            <div class="card">
                                <div class="card-header d-flex p-0">
                                    <h3 class="card-title p-3"></h3>
                                    <ul class="nav nav-pills ml-auto p-2">
                                        <button type="button" onclick="recargar()" class="btn btn-success"  style="margin-right: 25px"><i class="fa fa-redo-alt"></i> Recargar</button>
                                        <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Base Presupuesto</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Nuevos Materiales</a></li>

                                    </ul>
                                </div><!-- /.card-header -->
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab_1">

                                            <!-- inicio -->
                                            <div>

                                                <form>
                                                    <div class="card-body">


                                                        @foreach($rubro as $item)

                                                            <div class="accordion-group" data-behavior="accordion">

                                                                <label class="accordion-header" style="background-color: #c5c6c8; color: black !important;">{{ $item->numero }} - {{ $item->nombre }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ${{ $item->sumarubro }}</label>

                                                                <!-- foreach para cuenta -->
                                                                <div class="accordion-body">

                                                                    @foreach($item->cuenta as $cc)

                                                                        <div class="accordion-group" data-behavior="accordion" data-multiple="true">
                                                                            <p class="accordion-header" style="background-color: #b0c2f2; color: black !important;">{{ $cc->numero }} - {{ $cc->nombre }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ${{ $cc->sumaobjetototal }}</p>

                                                                            <div class="accordion-body">
                                                                                <div class="accordion-group" data-behavior="accordion" data-multiple="true">

                                                                                    <!-- foreach para objetos -->
                                                                                    @foreach($cc->objeto as $obj)

                                                                                        <p class="accordion-header" style="background-color: #b0f2c2; color: black !important;">{{ $obj->numero }} | {{ $obj->nombre }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ${{ $obj->sumaobjeto }}</p>
                                                                                        <div class="accordion-body">

                                                                                            <table data-toggle="table">
                                                                                                <thead>
                                                                                                <tr>
                                                                                                    <th style="width: 30%; text-align: center">Descripción</th>
                                                                                                    <th style="width: 20%; text-align: center">U/M</th>
                                                                                                    <th style="width: 15%; text-align: center">Costo</th>
                                                                                                    <th style="width: 10%; text-align: center">Unidades</th>
                                                                                                    <th style="width: 10%; text-align: center">Periodo</th>
                                                                                                    <th style="width: 10%; text-align: center">Total</th>

                                                                                                </tr>
                                                                                                </thead>
                                                                                                <tbody>

                                                                                                <!-- foreach para material -->

                                                                                                @foreach($obj->material as $mm)

                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <input type="hidden" name="idmaterial[]" value='{{ $mm->id }}'>
                                                                                                            <input value="{{ $mm->descripcion }}" disabled class="form-control" type="text">
                                                                                                        </td>
                                                                                                        <td><input value="{{ $mm->unimedida }}" disabled class="form-control"  type="text"></td>
                                                                                                        <td><input value="{{ $mm->costo }}" disabled class="form-control" style="max-width: 170px" ></td>
                                                                                                        <td><input value="{{ $mm->cantidad }}" disabled name="unidades[]" class="form-control" type="number" onchange="multiplicar(this)" maxlength="6"  style="max-width: 180px" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"></td>
                                                                                                        <td><input value="{{ $mm->periodo }}" disabled name="periodo[]" class="form-control" min="1" type="number" onchange="multiplicar(this)" maxlength="6"  style="max-width: 180px" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"></td>
                                                                                                        <td><input value="{{ $mm->total }}" disabled name="total[]" class="form-control" type="text" style="max-width: 180px"></td>
                                                                                                    </tr>

                                                                                                    <!-- fin foreach material -->
                                                                                                @endforeach

                                                                                                </tbody>

                                                                                            </table>

                                                                                        </div>

                                                                                @endforeach
                                                                                <!-- finaliza foreach para objetos-->

                                                                                </div>
                                                                            </div>


                                                                        </div>

                                                                @endforeach
                                                                <!-- fin foreach para cuenta -->
                                                                </div>
                                                            </div>

                                                            @if($loop->last)
                                                                <script>
                                                                    setTimeout(function () {
                                                                        mostrarContenedor();
                                                                        closeLoading();
                                                                    }, 1000);
                                                                </script>
                                                        @endif

                                                    @endforeach
                                                    <!-- fin foreach para rubro -->

                                                    </div>
                                                </form>
                                            </div>
                                        </div>


                                        <!-- LISTA DE NUEVOS MATERIALES - TABS 2 -->
                                        <div class="tab-pane" id="tab_2">

                                            <form>
                                                <div class="card-body">
                                                    <table class="table" id="matrizMateriales" data-toggle="table">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 15%; text-align: center">Cod</th>
                                                            <th style="width: 30%; text-align: center">Descripción</th>
                                                            <th style="width: 12%; text-align: center">U/M</th>
                                                            <th style="width: 14%; text-align: center">Costo</th>
                                                            <th style="width: 14%; text-align: center">Cantidad</th>
                                                            <th style="width: 9%; text-align: center">Periodo</th>

                                                            <th style="width: 10%; text-align: center">Opciones</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="myTbodyMateriales">

                                                        @foreach($listado as $ll)

                                                            <tr>
                                                                <td><select class="form-control seleccion" style="max-width: 180px">
                                                                        @foreach($objeto as $item)
                                                                            <option value="{{$item->id}}">{{$item->codigo}} - {{ $item->nombre }}</option>
                                                                        @endforeach
                                                                    </select></td>
                                                                <td>
                                                                    <input name="idfila[]" value="{{ $ll->id }}" type="hidden">
                                                                    <input disabled value="{{ $ll->descripcion }}" class="form-control" type="text">
                                                                </td>
                                                                <td>
                                                                    <input disabled value="{{ $ll->simbolo }}" class="form-control" type="text">
                                                                </td>
                                                                <td><input disabled value="{{ $ll->costo }}" class="form-control" min="1" type="number" style="max-width: 120px"></td>
                                                                <td><input disabled value="{{ $ll->cantidad }}" class="form-control" min="1" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" type="number" style="max-width: 120px"></td>
                                                                <td><input disabled value="{{ $ll->periodo }}" class="form-control" min="1" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;" type="number" style="max-width: 180px"></td>

                                                                <td><button type="button" class="btn btn-block btn-success" id="btnTransferir" onclick="verificarTransferir(this)">Transferir</button></td>
                                                            </tr>

                                                        @endforeach

                                                        </tbody>

                                                    </table>

                                                </div>

                                            </form>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>



<script src="{{ asset('js/jquery.simpleaccordion.js') }}"></script>

<script>

    $(document).ready(function() {
        $('[data-behavior=accordion]').simpleAccordion({cbOpen:accOpen, cbClose:accClose});
    });

    function accClose(e, $this) {
        $this.find('span').fadeIn(200);
    }

    function accOpen(e, $this) {
        $this.find('span').fadeOut(200)
    }

    function mostrarContenedor(){
        document.getElementById("bloquecontenedor").style.display = "block";
    }

    function recargar(){
        location.reload();
    }

</script>
