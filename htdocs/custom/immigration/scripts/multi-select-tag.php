
<style>


.select {
    margin-top: 10px;
  position: relative;
  min-width: 300px;
}
.select svg {
  position: absolute;
  right: 12px;
  top: calc(50% - 3px);
  width: 10px;
  height: 6px;
  stroke-width: 2px;
  stroke: #9098a9;
  fill: none;
  stroke-linecap: round;
  stroke-linejoin: round;
  pointer-events: none;
}
.select select {
    margin: 20px 0 20px 0;
  -webkit-appearance: none;
  padding: 7px 40px 7px 12px;
  width: 100%;
  border: 1px solid #e8eaed;
  border-radius: 5px;
  background: #fff;
  box-shadow: 3px 3px 3px -2px #9098a9;
  cursor: pointer;
  font-family: inherit;
  font-size: 16px;
  transition: all 150ms ease;
}
.select select:required:invalid {
  color: #5a667f;
}
.select select option {
  color: #223254;
}

.select select option:hover {
  color: #9B75A7;
}

.select select option[value=""][disabled] {
  display: none;
}
.select select:focus {
  outline: none;
  border-color: #07f;
  box-shadow: 0 0 0 2px rgba(0,119,255,0.2);
}
.select select:hover + svg {
  stroke: #07f;
}
.sprites {
  position: absolute;
  width: 0;
  height: 0;
  pointer-events: none;
  user-select: none;
}





	select {width: 100%;}
	.mult-select-tag{
    display:flex;
    width:100%;
    flex-direction:column;
    align-items:center;
    position:relative;
    --tw-shadow:0 1px 3px 0 rgb(0 0 0 / 0.1),0 1px 2px -1px rgb(0 0 0 / 0.1);
    --tw-shadow-color:0 1px 3px 0 var(--tw-shadow-color),0 1px 2px -1px var(--tw-shadow-color);
    --border-color:rgb(218, 221, 224);
    font-family:Verdana,sans-serif
}

.mult-select-tag .wrapper{
    width:100%
}

.mult-select-tag .body{
    display:flex;
    border:1px solid var(--border-color);
    background:#fff;
    min-height:2.15rem;
    width:100%;
    min-width:14rem
}

.mult-select-tag .input-container{
    display:flex;
    flex-wrap:wrap;
    flex:1 1 auto;
    padding:.1rem
}

.mult-select-tag .input-body{
    display:flex;
    width:100%
}

.mult-select-tag .input{
    flex:1;
    background:0 0;
    border-radius:.25rem;
    padding:.45rem;
    margin:10px;
    color:#2d3748;
    outline:0;
    border:1px solid var(--border-color)
}

.mult-select-tag .btn-container{
    color:#e2ebf0;
    padding:.5rem;
    display:flex;
    border-left:1px solid var(--border-color)
}

.mult-select-tag button{
    cursor:pointer;
    width:100%;
    color:#000000;
    outline:0;
    height:100%;
    border:none;
    padding:0;
    background:0 0;
    background-image:none;
    -webkit-appearance:none;
    text-transform:none;
    margin:0
}

.mult-select-tag button:first-child{
    width:1rem;
    height:90%
}

.mult-select-tag .drawer{
    position:absolute;
    background:#fff;
    max-height:15rem;
    z-index:40;
    top:98%;
    width:100%;
    overflow-y:scroll;
    border:1px solid var(--border-color);
    border-radius:.25rem
}

.mult-select-tag ul{
    list-style-type:none;
    padding:.5rem;margin:0
}

.mult-select-tag ul li{
    padding:.5rem;
    border-radius:.25rem;
    cursor:pointer
}

.mult-select-tag ul li:hover{
    background:rgb(243 244 246)
}

.mult-select-tag .item-container{
    display:flex;
    justify-content:center;
    align-items:center;
    color:#ffffff;
    padding:.2rem .4rem;
    margin:.2rem;
    font-weight:500;
    border:1px solid #4637c9;
    background:#21087a;
    border-radius:9999px
}

.mult-select-tag .item-label{max-width:100%;line-height:1;font-size:.75rem;font-weight:400;flex:0 1 auto;color:#ffffff}.mult-select-tag .item-close-container{display:flex;flex:1 1 auto;flex-direction:row-reverse}.mult-select-tag .item-close-svg{width:1rem;margin-left:.5rem;height:1rem;cursor:pointer;border-radius:9999px;display:block}.hidden{display:none}.mult-select-tag .shadow{box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.mult-select-tag .rounded{border-radius:.375rem}

</style>