import React from "react";
import { Chart } from "react-google-charts";
import Spinner from 'react-bootstrap/Spinner';
import { Card } from "react-bootstrap";

//define the props
interface WaterIntakeChartProps {
    data?: any[];
    title?: string;
    vAxisTitle?: string;
    hAxisTitle?: string;
    chartId?: string;
}

//define the component
const WaterIntakeChart: React.FC<WaterIntakeChartProps> = ({ data, title, vAxisTitle, hAxisTitle, chartId }) => {
    //define the options of the chart
    const options = {
        title: title ? title : "Consumo de água no período",
        vAxis: { title: vAxisTitle ? vAxisTitle : "Qtde (ml)" },
        hAxis: { title: hAxisTitle ? hAxisTitle : "Dia" },
        graph_id: chartId ? chartId : "WaterIntakeChart",
        seriesType: "bars",
        series: { 1: { type: "line" } },
        pointSize: 10,
        animation: {
            duration: 1500,
            easing: "InAndOut",
            startup: true,
        }
    };

    return (
        <>
            <Card className="w-100">
                {data !== undefined && data.length > 0 ? (
                    <Card.Body id={'chart_' + chartId} className={'d-flex align-items-center justify-content-center text-center'}>
                        <Chart
                            chartType="ComboChart"
                            rootProps={{ "data-testid": chartId }}
                            width={"100%"}
                            height={"100%"}
                            loader={
                                <Card.Body className='text-center loading'>
                                    <Spinner animation="border" role="status">
                                        <span className="sr-only">Loading...</span>
                                    </Spinner>
                                </Card.Body>
                            }
                            data={data}
                            options={options}
                        />
                    </Card.Body>
                ) : (
                    <Card.Body>
                        <table className='table table-striped table-hover'>
                            <tbody>
                                <tr>
                                    <td colSpan={3} className='nodata text-center d-flex align-items-center justify-content-center'>
                                        <Card.Text>Nenhum registro encontrado</Card.Text>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </Card.Body>
                )}
            </Card>
        </>
    )
}

export default WaterIntakeChart
