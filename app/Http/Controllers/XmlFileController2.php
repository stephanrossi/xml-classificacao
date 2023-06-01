<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\File;

class XmlFileController2 extends Controller
{
    public function processSpreadsheet(Request $request)
    {
        try {
            // Verifique se o caminho XML foi fornecido
            if (!$request->has('xmlPath')) {
                return response()->json([
                    'message' => 'O caminho da pasta dos XML é necessário.',
                ], 400);
            }

            if (!$request->has('spreadsheet')) {
                return response()->json([
                    'message' => 'Selecione a planilha de classificação.',
                ], 400);
            }

            $xmlFolderPath = $request->input('xmlPath');

            // Carregue o arquivo da planilha
            $spreadsheet = IOFactory::load($request->file('spreadsheet'));

            // Obtenha a primeira planilha (assumindo que os dados estão na primeira planilha)
            $worksheet = $spreadsheet->getActiveSheet();

            // Percorra cada linha da planilha
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set

                $row = [];
                foreach ($cellIterator as $cell) {
                    $row[] = $cell->getValue();
                }

                // Obtenha o número da NF-e, o tipo de entrada e a classificação contábil da linha atual
                $numeroNFe = $row[0];
                $tipoEntrada = trim(str_replace(' ', '_', $row[9])); // adaptar conforme a posição correta dos dados na sua planilha
                $classificacaoContabil = trim(str_replace(' ', '_', $row[10])); // adaptar conforme a posição correta dos dados na sua planilha

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
            return response()->json([
                'message' => 'Planilha processada com sucesso e arquivos XML movidos conforme necessário.',
            ]);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }
}
