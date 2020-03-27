<?php

namespace controllers;

use Exception;
use classes\Event;
use classes\Functions as fs;

class IndexController extends PageController
{
    private $events;
    private $eventDay;
    private $eventDaysNum;

    public function content(array $args = [])
    {
        try {
            [$events, $eventDay, $eventDaysNum] = Event::getAll(true, true);
        } catch (Exception $e) {
            fs::log($e->getMessage());
            header("Location: /error");
        }

        $data = [
            'events'       => $events,
            'eventDay'     => $eventDay,
            'eventDaysNum' => $eventDaysNum,
        ];

        $this->events       = $events;
        $this->eventDay     = $eventDay;
        $this->eventDaysNum = $eventDaysNum;

        return parent::content(array_merge($args, $data));
    }

    public function pagination($alignment = 'top')
    {
        if (empty($this->events)) {
            return "";
        }

        $return = "";

        $darkClass = DARK_THEME ? " pg-dark" : "";
        $nextNum   = $this->eventDay + 1;

        if ($alignment === 'top') {
            $prevText     = fs::t("Previous");
            $prevNum      = $this->eventDay - 1;
            $prevDisabled = $this->eventDay <= 1 ? " disabled" : "";
            $nextText     = fs::t("Next");
            $nextDisabled = $this->eventDay >= $this->eventDaysNum ? " disabled" : "";
            $srpActive    = fs::getOption('foundAll') ? " active" : "";
            $eventDayText = fs::t("Matchday") . " " . $this->eventDay;

            $return = <<< HTML
			<section class="container pagin">
				<div class="row">
					<div class="col-12">
						<nav aria-label="Matchday pagination">
							<ul class="pagination{$darkClass}">
								<li class="page-item left{$prevDisabled}">
									<a class="page-link preload" href="/index/{$prevNum}">< {$prevText}</a>
								</li>
								<li class="page-item active center">
									<span class="srp4 page-link{$srpActive}">{$eventDayText}</span>
								</li>
								<li class="page-item right{$nextDisabled}">
									<a class="page-link preload" href="/index/{$nextNum}">{$nextText} ></a>
								</li>
							</ul>
						</nav>
					</div>
				</div>
			</section>
HTML;
        } else {
            if ($alignment === 'bottom') {
                $nextMDText = fs::t("Matchday") . " " . $nextNum;

                if ($this->eventDay < $this->eventDaysNum) {
                    $return = <<< HTML
				<section class="container pagin mt-5">
					<div class="row">
						<div class="col-12">
							<nav aria-label="Matchday pagination">
								<ul class="pagination{$darkClass}">
									<li class="page-item center">
										<a class="page-link preload" href="/index/{$nextNum}">
											{$nextMDText} >
										</a>
									</li>
								</ul>
							</nav>
						</div>
					</div>
				</section>
HTML;
                }
            }
        }

        return $return;
    }
}
