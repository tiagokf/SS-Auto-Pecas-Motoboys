<?php
// relatorios/index.php
$pageTitle = 'Relatórios de Desempenho';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Definir datas padrão (últimos 30 dias)
$data_fim_default = date('Y-m-d');
$data_inicio_default = date('Y-m-d', strtotime('-30 days'));

// Parâmetros de filtragem
$filtro_data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : $data_inicio_default;
$filtro_data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : $data_fim_default;
$filtro_status = isset($_GET['status']) ? $_GET['status'] : 'todos';

// Buscar motoboys para o filtro e gráficos
$motoboys = fetchAll("SELECT id, nome FROM motoboys ORDER BY nome");

// Construir a consulta SQL para os dados do gráfico
$sql_total_movimentos = "
    SELECT mb.id, mb.nome, COUNT(m.id) as total_movimentos
    FROM motoboys mb
    LEFT JOIN movimentos m ON mb.id = m.motoboy_id
    WHERE 1=1
";

// Adicionar filtros à consulta
if (!empty($filtro_data_inicio)) {
    $data_inicio = date('Y-m-d 00:00:00', strtotime($filtro_data_inicio));
    $sql_total_movimentos .= " AND (m.data_hora_saida IS NULL OR m.data_hora_saida >= '$data_inicio')";
}

if (!empty($filtro_data_fim)) {
    $data_fim = date('Y-m-d 23:59:59', strtotime($filtro_data_fim));
    $sql_total_movimentos .= " AND (m.data_hora_saida IS NULL OR m.data_hora_saida <= '$data_fim')";
}

if ($filtro_status === 'abertos') {
    $sql_total_movimentos .= " AND m.data_hora_retorno IS NULL";
} elseif ($filtro_status === 'finalizados') {
    $sql_total_movimentos .= " AND m.data_hora_retorno IS NOT NULL";
}

$sql_total_movimentos .= " GROUP BY mb.id, mb.nome ORDER BY total_movimentos DESC";

// Buscar dados para o gráfico
$dados_grafico = fetchAll($sql_total_movimentos);

// Construir a consulta SQL para os dados de movimentos diários
$sql_movimentos_diarios = "
    SELECT 
        DATE(m.data_hora_saida) as data,
        mb.id as motoboy_id,
        mb.nome as motoboy_nome,
        COUNT(m.id) as total_movimentos
    FROM motoboys mb
    LEFT JOIN movimentos m ON mb.id = m.motoboy_id
    WHERE m.data_hora_saida IS NOT NULL
";

// Adicionar filtros à consulta
if (!empty($filtro_data_inicio)) {
    $data_inicio = date('Y-m-d 00:00:00', strtotime($filtro_data_inicio));
    $sql_movimentos_diarios .= " AND m.data_hora_saida >= '$data_inicio'";
}

if (!empty($filtro_data_fim)) {
    $data_fim = date('Y-m-d 23:59:59', strtotime($filtro_data_fim));
    $sql_movimentos_diarios .= " AND m.data_hora_saida <= '$data_fim'";
}

if ($filtro_status === 'abertos') {
    $sql_movimentos_diarios .= " AND m.data_hora_retorno IS NULL";
} elseif ($filtro_status === 'finalizados') {
    $sql_movimentos_diarios .= " AND m.data_hora_retorno IS NOT NULL";
}

$sql_movimentos_diarios .= " GROUP BY DATE(m.data_hora_saida), mb.id, mb.nome ORDER BY data, mb.nome";

// Buscar dados diários para o gráfico de linhas
$dados_diarios = fetchAll($sql_movimentos_diarios);

// Processar dados para o gráfico de linhas
$datas_unicas = [];
$motoboys_dados = [];

// Inicializar array para cada motoboy
foreach ($motoboys as $motoboy) {
    $motoboys_dados[$motoboy['id']] = [
        'nome' => $motoboy['nome'],
        'dados' => []
    ];
}

// Agrupar os dados por data
foreach ($dados_diarios as $movimento) {
    $data = $movimento['data'];
    $motoboy_id = $movimento['motoboy_id'];
    $total = $movimento['total_movimentos'];
    
    if (!in_array($data, $datas_unicas)) {
        $datas_unicas[] = $data;
    }
    
    if (isset($motoboys_dados[$motoboy_id])) {
        $motoboys_dados[$motoboy_id]['dados'][$data] = $total;
    }
}

// Ordenar as datas
sort($datas_unicas);

// Preparar dados para o gráfico de linhas em formato JSON
$dados_grafico_linhas = [];
foreach ($motoboys_dados as $motoboy_id => $motoboy) {
    if (count($motoboy['dados']) > 0) { // Só incluir motoboys com movimentos
        $serie = [
            'name' => $motoboy['nome'],
            'data' => []
        ];
        
        foreach ($datas_unicas as $data) {
            $serie['data'][] = isset($motoboy['dados'][$data]) ? (int)$motoboy['dados'][$data] : 0;
        }
        
        $dados_grafico_linhas[] = $serie;
    }
}

// Formatação das datas para o eixo X do gráfico de linhas
$datas_formatadas = [];
foreach ($datas_unicas as $data) {
    $datas_formatadas[] = date('d/m/Y', strtotime($data));
}

// Total de movimentos no período
$total_movimentos_periodo = 0;
foreach ($dados_grafico as $motoboy) {
    $total_movimentos_periodo += $motoboy['total_movimentos'];
}
?>

<!-- Conteúdo Principal -->
<div class="overflow-x-hidden overflow-y-auto bg-gray-100 flex-1">
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center">
            <h3 class="text-gray-700 text-3xl font-medium">Relatórios de Desempenho</h3>
        </div>

        <!-- Filtros -->
        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                    <input type="date" id="data_inicio" name="data_inicio" value="<?php echo $filtro_data_inicio; ?>"
                        class="w-full border border-gray-300 rounded-md p-2">
                </div>

                <div>
                    <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                    <input type="date" id="data_fim" name="data_fim" value="<?php echo $filtro_data_fim; ?>"
                        class="w-full border border-gray-300 rounded-md p-2">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="w-full border border-gray-300 rounded-md p-2">
                        <option value="todos" <?php echo $filtro_status === 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="abertos" <?php echo $filtro_status === 'abertos' ? 'selected' : ''; ?>>Abertos</option>
                        <option value="finalizados" <?php echo $filtro_status === 'finalizados' ? 'selected' : ''; ?>>Finalizados</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex justify-end">
                    <button type="button" onclick="window.location.href='index.php'"
                        class="px-4 py-2 text-gray-700 border border-gray-300 rounded mr-2 hover:bg-gray-100">
                        <i class="fas fa-undo mr-2"></i>Limpar Filtros
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Resumo dos dados -->
        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <h4 class="text-lg font-medium mb-4">Resumo do Período: <?php echo date('d/m/Y', strtotime($filtro_data_inicio)) . ' até ' . date('d/m/Y', strtotime($filtro_data_fim)); ?></h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <p class="text-sm text-blue-600 mb-1">Total de Movimentos</p>
                    <p class="text-2xl font-bold"><?php echo $total_movimentos_periodo; ?></p>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                    <p class="text-sm text-green-600 mb-1">Total de Motoboys</p>
                    <p class="text-2xl font-bold"><?php echo count($motoboys); ?></p>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                    <p class="text-sm text-purple-600 mb-1">Total de Dias</p>
                    <p class="text-2xl font-bold"><?php echo count($datas_unicas); ?></p>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                    <p class="text-sm text-yellow-600 mb-1">Média Diária</p>
                    <p class="text-2xl font-bold">
                        <?php 
                        echo count($datas_unicas) > 0 
                            ? number_format($total_movimentos_periodo / count($datas_unicas), 1) 
                            : '0.0'; 
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Gráfico de Barras - Total de Movimentos por Motoboy -->
        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <h4 class="text-lg font-medium mb-4">Total de Movimentos por Motoboy</h4>
            <div id="chart-total-movimentos" class="w-full" style="height: 400px;"></div>
        </div>

        <!-- Gráfico de Linhas - Evolução Diária -->
        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <h4 class="text-lg font-medium mb-4">Evolução Diária de Movimentos</h4>
            <div id="chart-evolucao-diaria" class="w-full" style="height: 400px;"></div>
        </div>

        <!-- Tabela de Desempenho -->
        <div class="mt-6 bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200">
                <h4 class="text-lg font-medium">Tabela de Desempenho</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Motoboy
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total de Movimentos
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Participação
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Média Diária
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($dados_grafico)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    Nenhum dado encontrado para o período selecionado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dados_grafico as $index => $motoboy): ?>
                                <tr class="<?php echo $index % 2 === 0 ? 'bg-white' : 'bg-gray-50'; ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($motoboy['nome']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900 font-medium">
                                            <?php echo $motoboy['total_movimentos']; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php 
                                        $percentual = $total_movimentos_periodo > 0 
                                            ? ($motoboy['total_movimentos'] / $total_movimentos_periodo) * 100 
                                            : 0;
                                        ?>
                                        <div class="text-sm text-gray-900">
                                            <?php echo number_format($percentual, 1); ?>%
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo $percentual; ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900">
                                            <?php 
                                            echo count($datas_unicas) > 0 
                                                ? number_format($motoboy['total_movimentos'] / count($datas_unicas), 1) 
                                                : '0.0'; 
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Incluir ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dados para o gráfico de total de movimentos
        const dadosGraficoBarras = <?php echo json_encode($dados_grafico); ?>;
        
        // Preparar dados para o gráfico de barras
        const categorias = dadosGraficoBarras.map(item => item.nome);
        const valores = dadosGraficoBarras.map(item => parseInt(item.total_movimentos));
        
        // Configuração do gráfico de barras
        const opcoesGraficoBarras = {
            series: [{
                name: 'Total de Movimentos',
                data: valores
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '70%',
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            colors: ['#4F46E5'],
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val;
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            xaxis: {
                categories: categorias,
                position: 'bottom',
                labels: {
                    rotate: -45,
                    rotateAlways: false,
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#D8E3F0',
                            colorTo: '#BED1E6',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5,
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                }
            },
            yaxis: {
                title: {
                    text: 'Total de Movimentos'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " movimentos";
                    }
                }
            },
            title: {
                text: 'Desempenho por Motoboy',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold'
                }
            }
        };
        
        // Renderizar o gráfico de barras
        if (valores.length > 0) {
            const graficoBarras = new ApexCharts(document.querySelector("#chart-total-movimentos"), opcoesGraficoBarras);
            graficoBarras.render();
        } else {
            document.querySelector("#chart-total-movimentos").innerHTML = '<div class="flex justify-center items-center h-full"><p class="text-gray-500">Nenhum dado disponível para o período selecionado</p></div>';
        }
        
        // Dados para o gráfico de linhas
        const datasFormatadas = <?php echo json_encode($datas_formatadas); ?>;
        const dadosGraficoLinhas = <?php echo json_encode($dados_grafico_linhas); ?>;
        
        // Configuração do gráfico de linhas
        const opcoesGraficoLinhas = {
            series: dadosGraficoLinhas,
            chart: {
                height: 350,
                type: 'line',
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 18,
                    left: 7,
                    blur: 10,
                    opacity: 0.2
                },
                toolbar: {
                    show: false
                }
            },
            colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F43F5E'],
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            title: {
                text: 'Motoboys',
                align: 'left',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold'
                }
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            markers: {
                size: 4
            },
            xaxis: {
                categories: datasFormatadas,
                title: {
                    text: 'Data'
                },
                labels: {
                    rotate: -45,
                    rotateAlways: false,
                }
            },
            yaxis: {
                title: {
                    text: 'Movimentos'
                },
                min: 0
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " movimentos";
                    }
                }
            }
        };
        
        // Renderizar o gráfico de linhas
        if (dadosGraficoLinhas.length > 0) {
            const graficoLinhas = new ApexCharts(document.querySelector("#chart-evolucao-diaria"), opcoesGraficoLinhas);
            graficoLinhas.render();
        } else {
            document.querySelector("#chart-evolucao-diaria").innerHTML = '<div class="flex justify-center items-center h-full"><p class="text-gray-500">Nenhum dado disponível para o período selecionado</p></div>';
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>