package com.blackmamba.ws;

import javax.jws.WebService;

@WebService(endpointInterface = "com.blackmamba.ws.HelloWorld")
public class HelloWorldImpl implements HelloWorld {
    @Override
    public String getHelloWorldAsString(String name) {
        return "Hello, World JAX-WS " + name + " HUYUUUU";
    }
}
