<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AldeiaTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Desativar verificações de chave estrangeira para melhor performance
        Schema::disableForeignKeyConstraints();
        DB::table('aldeia')->truncate(); // Limpar a tabela antes de inserir
        Schema::enableForeignKeyConstraints();

        // Caminho do arquivo CSV
        $csvFile = storage_path('../csv/aldeias.csv');

        // Verificar se o arquivo existe
        if (! file_exists($csvFile)) {
            $this->command->error("Arquivo CSV não encontrado: {$csvFile}");
            $this->command->info("Por favor, coloque o arquivo aldeias.csv em: storage/app/csv/");
            return;
        }

        // Abrir o arquivo CSV
        $file = fopen($csvFile, 'r');

        if (! $file) {
            $this->command->error("Erro ao abrir o arquivo: {$csvFile}");
            return;
        }

        // Ler o cabeçalho
        $header = fgetcsv($file, 0, ';');

        // Remover BOM se existir
        $header = array_map(function ($value) {
            return preg_replace('/\x{FEFF}/u', '', trim($value));
        }, $header);

        $this->command->info("Colunas detectadas no CSV: " . implode(', ', $header));
        $this->command->info("Total de colunas: " . count($header));

        $data = [];
        $batchSize = 500; // Processar em lotes para melhor performance
        $count = 0;
        $successCount = 0;
        $errorCount = 0;

        // Ler as linhas do CSV
        while (($row = fgetcsv($file, 0, ';')) !== false) {
            $count ++;

            // Ignorar linhas vazias
            if (empty(array_filter($row))) {
                $this->command->warn("Linha {$count} vazia - ignorada");
                continue;
            }

            // Preencher valores faltantes para manter o mesmo número de colunas
            while (count($row) < count($header)) {
                $row[] = null;
            }

            try {
                // Criar array associativo baseado no cabeçalho
                $rowData = array_combine($header, $row);

                // Preparar os dados para inserção
                $insertData = [
                    'cd_uf' => $this->cleanValue($rowData['CD_UF'] ?? null),
                    'nm_uf' => $this->cleanValue($rowData['NM_UF'] ?? null),
                    'cd_munic' => $this->cleanValue($rowData['CD_MUNIC'] ?? null),
                    'nm_munic' => $this->cleanValue($rowData['NM_MUNIC'] ?? null),
                    'id_li' => $this->cleanValue($rowData['ID_LI'] ?? null),
                    'cd_li' => $this->cleanValue($rowData['CD_LI'] ?? null),
                    'ocorrencia' => $this->cleanValue($rowData['OCORRENCIA'] ?? null),
                    'nome' => $this->cleanValue($rowData['NM_LI'] ?? null),
                    'cd_setor' => $this->cleanValue($rowData['CD_SETOR'] ?? null),
                    'situacao' => $this->cleanValue($rowData['SITUACAO'] ?? null),
                    'cd_sit' => $this->cleanValue($rowData['CD_SIT'] ?? null),
                    'cd_tipo' => $this->cleanValue($rowData['CD_TIPO'] ?? null),
                    'cd_aglom' => $this->cleanValue($rowData['CD_AGLOM'] ?? null),
                    'nm_aglom' => $this->cleanValue($rowData['NM_AGLOM'] ?? null),
                    'cd_ti' => $this->cleanValue($rowData['CD_TI'] ?? null),
                    'ti_funai' => $this->cleanValue($rowData['TI_FUNAI'] ?? null),
                    'nm_ti' => $this->cleanValue($rowData['NM_TI'] ?? null),
                    'fase' => $this->cleanValue($rowData['FASE'] ?? null),
                    'c_cr_funai' => $this->cleanValue($rowData['C_CR_FUNAI'] ?? null),
                    'n_cr_funai' => $this->cleanValue($rowData['N_CR_FUNAI'] ?? null),
                    'aldeia_funai' => $this->cleanValue($rowData['ALD_FUNAI'] ?? null),
                    'val_funai' => $this->cleanValue($rowData['VAL_FUNAI'] ?? null),
                    'aldeia_siasi' => $this->cleanValue($rowData['ALD_SIASI'] ?? null),
                    'val_siasi' => $this->cleanValue($rowData['VAL_SIASI'] ?? null),
                    'amz_leg' => $this->cleanValue($rowData['AMZ_LEG'] ?? null),
                    'lat' => $this->parseCoordinate($rowData['LAT'] ?? null),
                    'long' => $this->parseCoordinate($rowData['LONG'] ?? null),
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Validar dados obrigatórios
                if (empty($insertData['id_li']) || empty($insertData['nome'])) {
                    $this->command->warn("Linha {$count} ignorada - ID_LI ou NM_LI são obrigatórios");
                    $errorCount ++;
                    continue;
                }

                $data[] = $insertData;
                $successCount ++;

                // Inserir em lotes
                if (count($data) >= $batchSize) {
                    DB::table('aldeia')->insert($data);
                    $data = [];
                    $this->command->info("Processadas {$successCount} linhas...");
                }
            } catch (\Exception $e) {
                $this->command->error("Erro na linha {$count}: " . $e->getMessage());
                $this->command->info("Dados da linha: " . implode(';', $row));
                $errorCount ++;
            }
        }

        // Inserir os registros restantes
        if (! empty($data)) {
            try {
                DB::table('aldeia')->insert($data);
                $this->command->info("Último lote de " . count($data) . " registros inserido.");
            } catch (\Exception $e) {
                $this->command->error("Erro ao inserir último lote: " . $e->getMessage());
                $errorCount += count($data);
                $successCount -= count($data);
            }
        }

        fclose($file);

        // Resumo da importação
        $this->command->info("\n" . str_repeat("=", 50));
        $this->command->info("RESUMO DA IMPORTAÇÃO");
        $this->command->info(str_repeat("=", 50));
        $this->command->info("Total de linhas no CSV: {$count}");
        $this->command->info("Registros importados com sucesso: {$successCount}");
        $this->command->info("Erros/linhas ignoradas: {$errorCount}");
        $this->command->info("Importação de aldeias concluída!");
    }

    /**
     * Limpar e formatar valores
     */
    private function cleanValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim($value);

        // Converter para null se for string vazia após trim
        if ($value === '') {
            return null;
        }

        return $value;
    }

    /**
     * Parse coordenadas para formato decimal
     */
    private function parseCoordinate($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = $this->cleanValue($value);

        // Remover possíveis caracteres não numéricos (exceto ponto e sinal negativo)
        $value = preg_replace('/[^\d\.\-]/', '', $value);

        if (empty($value) || ! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}