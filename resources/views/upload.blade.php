<!DOCTYPE html>
<html>

<head>
    <title>Classificação automática de XMLs</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h3 class="text-center mt-5">Classificação automática de XMLs</h3>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @isset($message)
                <div class="alert alert-success mt-4">
                    {{$message}}
                </div>
                @endisset
                <form action="/upload" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="spreadsheet">Planilha:</label>
                        <input type="file" class="form-control" id="spreadsheet" name="spreadsheet" required>
                        <small>Planilha enviada pelo cliente, com o número da nota, tipo de entrada e classificação
                            contábil.</small>
                    </div>
                    <div class="form-group">
                        <label for="xmlPath">Caminho da pasta onde estão os XMLs:</label>
                        <input type="text" class="form-control" id="xmlPath" name="xmlPath" required>
                        <small>Exemplo: \\servidor13\COFRESIEG\XML\CNPJ\2023\Maio</small>
                    </div>
                    <div class="form-group">
                        <label for="columnLetterNFe">Letra da coluna com os números das NFe:</label>
                        <input type="text" class="form-control" id="columnLetterNFe" name="columnLetterNFe" required>
                        <small>Exemplo: A</small>
                    </div>
                    <div class="form-group">
                        <label for="columnLetterEntrada">Letra da coluna com os tipos de entrada:</label>
                        <input type="text" class="form-control" id="columnLetterEntrada" name="columnLetterEntrada"
                            required>
                        <small>Exemplo: AB</small>
                    </div>
                    <div class="form-group">
                        <label for="columnLetterClassificacao">Letra da coluna com as classificações contábeis:</label>
                        <input type="text" class="form-control" id="columnLetterClassificacao"
                            name="columnLetterClassificacao" required>
                        <small>Exemplo: O</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            </div>

        </div>
</body>

</html>
