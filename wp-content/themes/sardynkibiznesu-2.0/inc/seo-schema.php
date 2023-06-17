<?php

add_filter('wpseo_schema_block_sardynkibiznesu20/faq', 'render_faq_block_schema', 10, 3);
function render_faq_block_schema($graph, $block, $context) {
  $data = $block['attrs']['data'];
  $questions = [];
  for ($i = 0; $i < $data['faq_questions']; $i++) {
    $questions[] = array(
      "@type"=>'Question',
      "name"=>$data["faq_questions_{$i}_question"],
      "acceptedAnswer"=> array(
        "@type"=>"Answer",
        "text"=>$data["faq_questions_{$i}_answer"]
      )
    );
  }
  $graph[] = array(
    "@type"      => "FAQPage",
    "mainEntity" => $questions
  );

  return $graph;
}
