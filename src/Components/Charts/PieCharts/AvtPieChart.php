<?php
namespace Avetify\Components\Charts\PieCharts;

use Avetify\Components\Charts\AvtChartColors;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\PageRenderer;
use Avetify\Themes\Green\GreenTheme;
use Avetify\Themes\Main\ThemesManager;

class AvtPieChart implements PageRenderer {
    public function __construct(
        public string $key,
        public string $title,
        public array $labels,
        public array $values,
        public string $valueUnit
    ){}

    public function getTheme(): ThemesManager {
        return new GreenTheme();
    }

    public function renderPage(?string $title = null) {
        $theme = $this->getTheme();
        $theme->includesChartTools = true;
        $theme->placeHeader($title);

        $this->renderBody();
    }

    public function renderBody() {
        echo '<canvas ';
        HTMLInterface::addAttribute("id", $this->getCanvasId());
        HTMLInterface::closeTag();
        echo '</canvas>';

        ?>
        <script>
            const canvasId = '<?php echo $this->getCanvasId(); ?>';
            const title = '<?php echo $this->title; ?>';
            const valueUnit = '<?php echo $this->valueUnit; ?>';
            const labels = <?php echo json_encode($this->labels); ?>;
            const values = <?php echo json_encode($this->values); ?>;
            const bgColors = <?php echo json_encode(AvtChartColors::getDefaultColors(count($this->values))); ?>;
            const borderColors = <?php echo json_encode(AvtChartColors::getAlterColors(count($this->values))); ?>;

            const data = {
                labels: labels,
                datasets: [{
                    label: valueUnit,
                    data: values,
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: 2
                }]
            };

            const config = {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: title
                        }
                    }
                }
            }

            const myChart = new Chart(
                document.getElementById(canvasId),
                config
            );
        </script>
        <?php
    }

    public function getCanvasId() : string {
        return $this->key . "_canvas";
    }
}
