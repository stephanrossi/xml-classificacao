<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\File;

class XmlFileController3 extends Controller
{
    public function processSpreadsheet(Request $request)
    {
        // Verifique se todos os campos necessários foram fornecidos
        if (!$request->has(['xmlPath', 'columnLetterNFe', 'columnLetterEntrada', 'columnLetterClassificacao'])) {
            return response()->json([
                'message' => 'Todos os campos (xmlPath, columnLetterNFe, columnLetterEntrada, columnLetterClassificacao) são necessários.',
            ], 400);
        }

        $xmlFolderPath = $request->input('xmlPath');
        $columnLetterNFe = $request->input('columnLetterNFe');
        $columnLetterEntrada = $request->input('columnLetterEntrada');
        $columnLetterClassificacao = $request->input('columnLetterClassificacao');

        // Carregue o arquivo da planilha
        $spreadsheet = IOFactory::load($request->file('spreadsheet'));

        // Obtenha a primeira planilha (assumindo que os dados estão na primeira planilha)
        $worksheet = $spreadsheet->getActiveSheet();

        // Percorra cada linha da planilha
        foreach ($worksheet->getRowIterator() as $row) {
            // Obtenha o número da NF-e, o tipo de entrada e a classificação contábil da linha atual
            $numeroNFe = trim($worksheet->getCell($columnLetterNFe . $row->getRowIndex())->getValue());
            $tipoEntrada = trim(str_replace(' ', '_', $worksheet->getCell($columnLetterEntrada . $row->getRowIndex())->getValue()));
            $classificacaoContabil = trim(str_replace(' ', '_', $worksheet->getCell($columnLetterClassificacao . $row->getRowIndex())->getValue()));

            // Verifique se existe um arquivo XML com o mesmo nome do número da NF-e na pasta desejada
            $xmlPath = $xmlFolderPath . '/' . $numeroNFe . '.xml';

            if (File::exists($xmlPath)) {
                // Se o arquivo existir, crie uma nova pasta com o nome do tipo de entrada (se ainda não existir)
                $newFolderPath = $xmlFolderPath . '/' . $tipoEntrada;

                if (!File::exists($newFolderPath)) {
                    File::makeDirectory($newFolderPath, 0777, true);
                }

                // Crie uma subpasta com o nome da classificação contábil (se ainda não existir)
                $subFolderPath = $newFolderPath . '/' . $classificacaoContabil;

                if (!File::exists($subFolderPath)) {
                    File::makeDirectory($subFolderPath, 0777, true);
                }

                // Mova o arquivo XML para a subpasta
                File::move($xmlPath, $subFolderPath . '/' . $numeroNFe . '.xml');
            }
        }

        // Retornar uma resposta bem-sucedida
        // return response()->json([
        //     'message' => 'Planilha processada com sucesso e arquivos XML movidos conforme necessário.',
        // ]);

        return view('upload', ['message' => 'Planilha processada com sucesso e arquivos XML movidos conforme necessário.']);
    }
}
