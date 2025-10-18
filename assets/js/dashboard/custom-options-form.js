/**
 * custom-options-form
 */
(function ($) {
    $(window).on("custom-options-form", function () {

        if($('.custom-options-form tr[data-conditional-logic="true"]').length){
            $('.custom-options-form tr[data-conditional-logic="true"]').each(function(){
                let thisElement = $(this);
                let action = thisElement.attr('data-conditional-logic-action');
                let rules = JSON.parse(thisElement.attr('data-conditional-logic-rules'));
                const OP = {
                    '==': (a,b)=>a==b, '!=':(a,b)=>a!=b, '>':(a,b)=>(+a)>(+b), '<':(a,b)=>(+a)<(+b),
                    '>=':(a,b)=>(+a)>=(+b), '<=':(a,b)=>(+a)<=(+b),
                    contains:(a,b)=>String(a).includes(String(b)),
                    in:(a,b)=>{const A=Array.isArray(a)?a:[a];const B=Array.isArray(b)?b:String(b).split(',').map(s=>s.trim());return A.some(v=>B.includes(String(v)));},
                    not_in:(a,b)=>{const A=Array.isArray(a)?a:[a];const B=Array.isArray(b)?b:String(b).split(',').map(s=>s.trim());return !A.some(v=>B.includes(String(v)));},
                    empty:(a)=>Array.isArray(a)?a.length===0:(a===undefined||a===null||String(a).trim()===''),
                    not_empty:(a)=>!OP.empty(a)
                };
                const getEl = (n)=>{let $e=$('[name="'+n+'"]'); if(!$e.length)$e=$('#'+n); return $e;};
                const getVal = ($e)=>{
                    if(!$e.length) return '';
                    const t=($e.attr('type')||'').toLowerCase(), tag=($e.prop('tagName')||'').toLowerCase();
                    if(t==='checkbox') return $e.is(':checked')?'1':'0';
                    if(t==='radio'){const n=$e.attr('name'); return $('input[type="radio"][name="'+n+'"]:checked').val()||'';}
                    if(tag==='select') return $e.prop('multiple')?($e.val()||[]).map(String):String($e.val()||'');
                    return String($e.val()||'');
                };
                const evalRules = (r)=>{
                    if(!r) return true;
                    let list=r, rel='AND';
                    if(!Array.isArray(r) && r.rules){rel=String(r.relation||'AND').toUpperCase()==='OR'?'OR':'AND'; list=r.rules;}
                    const res=list.map(rule=>{
                        const fn=OP[(rule.operator||'==').toLowerCase()]||OP['=='];
                        return fn(getVal(getEl(rule.field)), rule.value);
                    });
                    return rel==='OR' ? res.some(Boolean) : res.every(Boolean);
                };
                const passed = evalRules(rules);
                const show = (String(action||'show').toLowerCase()==='show') ? passed : !passed;
                thisElement.toggle(!!show);
            });
        }

    });
})(jQuery);
