<?php
namespace Avetify\Components\Charts;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\PageRenderer;
use Avetify\Themes\Green\GreenTheme;
use Avetify\Themes\Main\ThemesManager;

class AvtChart implements PageRenderer {
    public function __construct(
        public string $key,
        public array $labels,
        public array $datasets,
        public float $min,
        public float $max
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
            const labels = <?php echo json_encode($this->labels); ?>;
            const datasets = <?php echo json_encode($this->datasets); ?>;
            const minValue = <?php echo $this->min; ?>;
            const maxValue = <?php echo $this->max; ?>;

            const data = {
                labels: labels,
                datasets: datasets
            };

            const config = {
                type: 'line',
                data: data,
                normalized: true,
                options: {
                    animation: false,
                    //showLine: false,
                    scales: {
                        y: {
                            type: 'linear',
                            min: minValue,
                            max: maxValue
                        }
                    }
                }
            };

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
