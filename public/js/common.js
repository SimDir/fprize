//var D = this;
//var AppH = 600;
var MsBo = 1;
function MsgBox(msg) {
    MsBo++;
//    console.log('MsBo=',MsBo);
    var div = document.createElement('div');
//    div.className = "snackbar";
    div.setAttribute("id", "snackbar");
    div.innerHTML = msg;
    document.body.appendChild(div);
    div.className = "show";
    div.style.cssText = "top: " + (10 + MsBo) + "%;";
    setTimeout(function () {
        MsBo--;
        document.body.removeChild(div);
//        div.className = div.className.replace("show", "");
    }, 5000);

}
;
if (window.requestIdleCallback) {
        //console.log('log IDLE start');
        requestIdleCallback(backgroundTask);
} 
// Функция обратного вызова requestIdleCallback
function backgroundTask(deadline) {

//setTimeout(sayHi, 15000);
}
function sayHi() {
    requestIdleCallback(backgroundTask);
    fetch('/index/time/', {
        mode: 'cors',
        method: 'POST',
        cache: 'no-cache',
        credentials: 'same-origin',
    })
//    .then(res => {
//        console.log(res);
//    });
    //console.log('log IDLE');
}

function execBodyScripts(body_el) {
    // Finds and executes scripts in a newly added element's body.
    // Needed since innerHTML does not run scripts.
    //
    // Argument body_el is an element in the dom.

    function nodeName(elem, name) {
        return elem.nodeName && elem.nodeName.toUpperCase() ===
                name.toUpperCase();
    };

    function evalScript(elem) {
        var data = (elem.text || elem.textContent || elem.innerHTML || ""),
                head = document.getElementsByTagName("head")[0] ||
                document.documentElement,
                script = document.createElement("script");

        script.type = "text/javascript";
        try {
            // doesn't work on ie...
            script.appendChild(document.createTextNode(data));
        } catch (e) {
            // IE has funky script nodes
            script.text = data;
        }

        head.insertBefore(script, head.firstChild);
        head.removeChild(script);
    };

    // main section of function
    var scripts = [],
            script,
            children_nodes = body_el.childNodes,
            child,
            i;

    for (i = 0; children_nodes[i]; i++) {
        child = children_nodes[i];
        if (nodeName(child, "script") &&
                (!child.type || child.type.toLowerCase() === "text/javascript")) {
            scripts.push(child);
        }
    }
//    console.log('e s as',scripts);
    for (i = 0; scripts[i]; i++) {
        script = scripts[i];
        if (script.parentNode) {
            script.parentNode.removeChild(script);
        }
        evalScript(scripts[i]);
    }
};

var mvcrbstore = new Vuex.Store({
    state: {
        content: '',
        MainMenu: {},
        User: {}
    },
    mutations: {
        content(state, n) {
//            console.log('n c s',state);
            state.content = n;
        },
        User(state, n) {
//            console.log('store sn',n);
            state.User = n;
        },
        MainMenu(state, n) {
//            console.log('store sn',n);
            state.MainMenu = n;
        }

    }
});

var mvcrbmixin = {
    data() {
        return {
            Count: 0, perPage: 10, currentPage: 0,
            searchStr: '', currentSortDir: 'asc', currentSort: 'id',LoadingData:false
        };
    },
    methods: {
        sort(s) {
            if (s === this.currentSort) {
                this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
            }
            this.currentSort = s;
            this.setPage(this.currentPage);
//            console.log(this.currentSortDir,this.currentSort);
        }
    },
    computed: {
        paginator() {
            let p = _.range(1, Math.ceil(this.Count / this.perPage) + 1);
            let st = this.currentPage - 1;
            if (st < 1)
                st = 1;
            if (st > (p.length-2))
                st = p.length-2;
            return {
                count: p.length,
                currentPage: this.currentPage,
                pages: p.slice(st-1, st +2)
            };
        }
    }
}