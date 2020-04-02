<?php

namespace controllers;

use Exception;
use classes\Frozen;
use classes\Functions as fs;

class TripsController extends PageController
{
    public function content(array $args = [])
    {
        $data['activities'] = self::frozenActivities();

        return parent::content(array_merge($args, $data));
    }

    public static function frozenActivities(bool $noCancelButton = false): array
    {
        try {
            $frozenArr = Frozen::getAll(USER_ID);
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return [];
        }

        $activities = [];
        foreach ($frozenArr as $frozen) {
            $id = implode(",", $frozen->id);

            $action = "dostarczenie i odbiór";
            $description = "Dostarczenie i odbiór od";
            $dateDesc = "Termin dostarczenia / odbioru:";
            $type = "Dostarczenie materiału oraz odbiór przyłbic";
            $bascinetQuantity = "<div class='col-12 mb-2'>Ilość przyłbic: <span>{$frozen->bascinet}</span></div>";
            $materialQuantity = "<div class='col-12 mb-2'>Ilość materiału: <span>{$frozen->material}</span></div>";
            if (empty($frozen->bascinet)) {
                $action = "dostarczenie";
                $description = "Dostarczenie dla";
                $dateDesc = "Termin dostarczenia:";
                $type = "Dostarczenie materiału";
                $bascinetQuantity = "";
            } else if (empty($frozen->material)) {
                $action = "odbiór";
                $description = "Odbiór od";
                $dateDesc = "Termin odbioru:";
                $type = "Odbiór przyłbic";
                $materialQuantity = "";
            }

            $date = $frozen->date->format("H:i - d.m.Y");

            $description .= " <span>{$frozen->producer->name}</span> (tel. <a href='tel:{$frozen->producer->tel}'>{$frozen->producer->tel}</a>)";

            $address = $frozen->producer->getAddress();
            $flat = !empty($address->building) ? "/$address->building" : "";
            $address = "{$address->street} {$address->building}{$flat}, {$address->city}";

            $cancelButton = "";
            if (!$noCancelButton) {
                $cancelButton = "<div class='button col-3'><a href='/ajax/map/delete' class='btn btn-transparent m-0 cancel'  data-id='{$id}' data-type='frozen'>Anuluj</a></div>";
            }

            $activities[] = <<< HTML
            <div class="activityBox trip container">
                <div class="content row">
                    <div class="text col">
                        <div class="row">
                            <div class="col-12 mb-2">
                                {$description}
                            </div>
                            <div class="col-12 mb-2">
                                {$dateDesc} <span>{$date}</span>
                            </div>
                            <div class="col-12 mb-2">
                                Adres: <span>{$address}</span>
                            </div>
                            <div class="col-12 mb-2">
                                Typ: <span>{$type}</span>
                            </div>
                            {$bascinetQuantity}
                            {$materialQuantity}
                        </div>
                    </div>
                    {$cancelButton}
                    <div class="col-12 text-right">
                        <button class="btn btn-red confirm">Potwierdź {$action}</button>
                    </div>
                </div>
            </div>
HTML;
        }

        if (empty($activities)) {
            $activities[] = <<< HTML
            <p class="no-frozen">Aktualnie nie masz zaplanowanych żadnych przjeazdów. Żeby zaplanować odbiór lub dostarczenie przejdż do mapy i wybierz producenta.</p>
HTML;

        }

        return $activities;
    }
}