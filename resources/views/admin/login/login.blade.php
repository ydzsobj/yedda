<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
<meta http-equiv="Pragma" content="no-cache"> 
<meta http-equiv="Cache-Control" content="no-cache"> 
<meta http-equiv="Expires" content="0"> 
<title>后台管理</title>
<link href="/css/admin/login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/layer/layer.js"></script>
</head>
<style>

canvas {
	position: absolute;
	width: 100%;
	height: 100%;
    background:#000;
    z-index: 1  ;
}

        .login_logo {
            transition: All 0.4s ease-in-out;
            -webkit-transition: All 0.4s ease-in-out;
            -moz-transition: All 0.4s ease-in-out;
            -o-transition: All 0.4s ease-in-out;
        }

        .login_logo:hover {
            transform: rotate(360deg);
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
        }
</style>

<style type="text/css">object,embed{                -webkit-animation-duration:.001s;-webkit-animation-name:playerInserted;                -ms-animation-duration:.001s;-ms-animation-name:playerInserted;                -o-animation-duration:.001s;-o-animation-name:playerInserted;                animation-duration:.001s;animation-name:playerInserted;}                @-webkit-keyframes playerInserted{from{opacity:0.99;}to{opacity:1;}}                @-ms-keyframes playerInserted{from{opacity:0.99;}to{opacity:1;}}                @-o-keyframes playerInserted{from{opacity:0.99;}to{opacity:1;}}                @keyframes playerInserted{from{opacity:0.99;}to{opacity:1;}}</style>


<body>

  @if(count($errors)>0)
                   
                        <script type="text/javascript">var errors='';</script>
                         @foreach($errors->all() as $error)
                         <script type="text/javascript">errors+='{{ $error }}'+"<br/>" </script>
                         @endforeach
                         <script type="text/javascript">layer.msg(errors)</script>    
  @endif
    
      <div class="login" style="top: 20%;z-index: 2;left: 50%;  position: absolute;transform: translateX(-50%);  background-color:rgb(0,0,0,0.5)">
          <div class="login_logo"><a href="#"><img src="/images/ydzs.jpg" /></a></div>
          <div class="login_name">
               <p style="color:#ffffff;">后台信息管理系统</p>
          </div>
          <form method="post" action="{{ route('login') }}">
              <input name="username" type="text"   placeholder="用户名"  value="{{old('username')}}" >
<!--               <span id="password_text" onclick="this.style.display='none';document.getElementById('password').style.display='block';" >密码</span>
 -->              <input name="password" type="password" id="password" placeholder="密码" value="" />
               <div class="form-group">
                <img src="{{captcha_src()}}" onclick="this.src='http://{{$_SERVER['SERVER_NAME']}}/captcha/default?l6rrkGyy'+Math.random()" style="margin-left: 80px;"> 
                 <br/>
                   <input type="text" class="form-control" id="captcha" placeholder="验证码" autocomplete="off" name="captcha">
               </div>
               {{csrf_field()}}
              <input value="登录" style="width:100%;background-color: #ffff33;color:black;" type="submit">
          </form>
      </div>
      <div class="copyright" style="z-index: 2;position: absolute;top: 85%;">一代宗师国医馆 版权所有©2016-2018 技术支持电话：000-00000000</div>
      <canvas width="1920" height="282"></canvas>


 <script>
         "use strict";
         {
         	// particles class
         	class Particle {
         		constructor(k, i, j) {
         			this.i = i;
         			this.j = j;
         			this.init();
         			this.x = this.x0;
         			this.y = this.y0;
         			this.pos = posArray.subarray(k * 3, k * 3 + 3);
         			this.pointer = pointer;
         		}
         		init() {
         			this.x0 = canvas.width * 0.5 + this.i * canvas.width / 240;
         			this.y0 = canvas.height * 0.5 + this.j * canvas.height / 160;
         		}
         		move() {
         			const dx = this.pointer.x - this.x;
         			const dy = this.pointer.y - this.y;
         			const d = Math.sqrt(dx * dx + dy * dy);
         			const s = 1000 / d;
         			this.x += -s * (dx / d) + (this.x0 - this.x) * 0.02;
         			this.y += -s * (dy / d) + (this.y0 - this.y) * 0.02;
         			// update buffer position
         			this.pos[0] = this.x;
         			this.pos[1] = this.y;
         			this.pos[2] = 0.15 * s * s;
         		}
         	}
         	// webGL canvas
         	const canvas = {
         		init(options) {
         			// set webGL context
         			this.elem = document.querySelector("canvas");
         			const gl = (this.gl =
         				this.elem.getContext("webgl", options) ||
         				this.elem.getContext("experimental-webgl", options));
         			if (!gl) return false;
         			// compile shaders
         			const vertexShader = gl.createShader(gl.VERTEX_SHADER);
         			gl.shaderSource(
         				vertexShader,
         				`
         					precision highp float;
         					attribute vec3 aPosition;
         					uniform vec2 uResolution;
         					void main() {
         						gl_PointSize = max(2.0, min(30.0, aPosition.z));
         						gl_Position = vec4(
         							( aPosition.x / uResolution.x * 2.0) - 1.0, 
         							(-aPosition.y / uResolution.y * 2.0) + 1.0, 
         							0.0,
         							1.0
         						);
         					}
               	`
         			);
         			gl.compileShader(vertexShader);
         			const fragmentShader = gl.createShader(gl.FRAGMENT_SHADER);
         			gl.shaderSource(
         				fragmentShader,
         				`
         					precision highp float;
         					void main() {
         						vec2 pc = 2.0 * gl_PointCoord - 1.0;
         						gl_FragColor = vec4(1.0, 0.85, 0.25, 1.0 - dot(pc, pc));
         					}
         				`
         			);
         			gl.compileShader(fragmentShader);
         			const program = (this.program = gl.createProgram());
         			gl.attachShader(this.program, vertexShader);
         			gl.attachShader(this.program, fragmentShader);
         			gl.linkProgram(this.program);
         			gl.useProgram(this.program);
         			// resolution
         			this.uResolution = gl.getUniformLocation(this.program, "uResolution");
         			gl.enableVertexAttribArray(this.uResolution);
         			// canvas resize
         			this.resize();
         			window.addEventListener("resize", () => this.resize(), false);
         			return gl;
         		},
         		resize() {
         			this.width = this.elem.width = this.elem.offsetWidth;
         			this.height = this.elem.height = this.elem.offsetHeight;
         			for (const p of particles) p.init();
         			this.gl.uniform2f(this.uResolution, this.width, this.height);
         			this.gl.viewport(
         				0,
         				0,
         				this.gl.drawingBufferWidth,
         				this.gl.drawingBufferHeight
         			);
         		}
         	};
         	const pointer = {
         		init(canvas) {
         			this.x = 0.1 + canvas.width * 0.5;
         			this.y = canvas.height * 0.5;
         			this.s = 0;
         			["mousemove"].forEach((event, touch) => {
                        document.querySelector("canvas").addEventListener(
         					event,
         					e => {
         						if (touch) {
                                     return false;
         							e.preventDefault();
         							this.x = e.targetTouches[0].clientX;
         							this.y = e.targetTouches[0].clientY;
         						} else {
         							this.x = e.clientX;
         							this.y = e.clientY;
         						}
         					},
         					false
         				);
         			});
         		}
         	};
         	// init webGL canvas
         	const particles = [];
         	const gl = canvas.init({
         		alpha: false,
         		stencil: true,
         		antialias: true,
         		depth: false
         	});
         	// additive blending "lighter"
         	gl.blendFunc(gl.SRC_ALPHA, gl.ONE);
         	gl.enable(gl.BLEND);
         	// init pointer
         	pointer.init(canvas);
         	// init particles
         	const nParticles = 240 * 80;
         	const posArray = new Float32Array(nParticles * 3);
         	let k = 0;
         	for (let i = -120; i < 120; i++) {
         		for (let j = -40; j < 40; j++) {
         			particles.push(new Particle(k++, i, j));
         		}
         	}
         	// create position buffer
         	const aPosition = gl.getAttribLocation(canvas.program, "aPosition");
         	gl.enableVertexAttribArray(aPosition);
         	const positionBuffer = gl.createBuffer();
         	// draw all particles
         	const draw = () => {
         		gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
         		gl.vertexAttribPointer(aPosition, 3, gl.FLOAT, false, 0, 0);
         		gl.bufferData(
         			gl.ARRAY_BUFFER,
         			posArray,
         			gl.DYNAMIC_DRAW
         		);
         		gl.drawArrays(gl.GL_POINTS, 0, nParticles);
         	}
         	// main animation loop
         	const run = () => {
         		requestAnimationFrame(run);
         		for (const p of particles) p.move();
         		draw();
         	};
         	requestAnimationFrame(run);
         }
</script>
</body>
</html>
