<?php
// Escape output to prevent XSS
function e(string $v): string  //  تضمن تستقبل متغير نصيا و ترجع :نص
{
     return htmlspecialchars($v, ENT_QUOTES); 
}

//لخيار ENT_QUOTES يضمن تحويل كل من علامات التنصيص المفردة ' والمزدوجة " إلى رموز مشفرة 
//(&#039; و &quot;)، بالإضافة إلى علامتي الأكبر والاصغر (< و >).
?>