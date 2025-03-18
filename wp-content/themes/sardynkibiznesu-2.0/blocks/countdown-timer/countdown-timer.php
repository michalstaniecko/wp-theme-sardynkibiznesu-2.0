<?php

$minuts = get_field('minutes');

?>
<div>
    <div class="countdown-timer" data-minutes="<?php echo $minuts; ?>">
        <div class="grid auto-cols-max grid-flow-col gap-0.5 md:gap-5 text-center justify-center">
<!--            <div class="text-white rounded bg-[#c00] flex flex-col p-2 text-sm md:text-lg font-semibold">-->
<!--    <span class="countdown font-mono text-4xl md:text-6xl font-normal">-->
<!--      <span class="countdown-timer__days">00</span>-->
<!--    </span>-->
<!--                dni-->
<!--            </div>-->
<!--            <div class="text-white rounded bg-[#c00] flex flex-col p-2 text-sm md:text-lg font-semibold">-->
<!--    <span class="countdown font-mono text-4xl md:text-6xl font-normal">-->
<!--      <span class="countdown-timer__hours">00</span>-->
<!--    </span>-->
<!--                godzin-->
<!--            </div>-->
            <div class="text-white rounded-[0.5rem] bg-[#c00] flex flex-col p-2 text-sm md:text-lg font-semibold">
    <span class="countdown font-mono text-4xl md:text-6xl font-normal">
      <span class="countdown-timer__minutes">00</span>
    </span>
                minut
            </div>
            <div class="text-white rounded-[0.5rem] bg-[#c00] flex flex-col p-2 text-sm md:text-lg font-semibold">
    <span class="countdown font-mono text-4xl md:text-6xl font-normal">
      <span class="countdown-timer__seconds">00</span>
    </span>
                sekund
            </div>
        </div>
    </div>

</div>
